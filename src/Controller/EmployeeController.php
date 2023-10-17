<?php

namespace App\Controller;

use \Pimcore\Model\DataObject;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Task;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\Document\Link;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pimcore\Model;
use Pimcore\Model\User;
use Pimcore\Tool\DeviceDetector;

class EmployeeController extends FrontendController
{
    #[Template('employee/home.html.twig')]
    public function defaultAction(Request $request): array
    {
        return [];
    }
    public function signIn(Request $request): Response
    {
        return $this->render('employee/signin.html.twig');
    }

    #[Template('employee/my-gallery.html.twig')]
    public function myGalleryAction(Request $request): array
    {
        if ('asset' === $request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));
            if ('folder' === $asset->getType()) {
                return [
                    'assets' => $asset->getChildren()
                ];
            }
        }

        return [];
    }

    #[Template('employee/footer.html.twig')]
    public function footerAction(Request $request): Response
    {
        return $this->render('employee/footer.html.twig');
    }

    /**
     * @Route("/employee", name="employee_preview")
     * @throws \Exception
     *
     */
    #[Template('employee/employee.html.twig')]
    public function employeeAction(Request $request): Response
    {
        $test = Employee::getById(3);
        $translated = $test->getDescription('hi');

        $blockItems = $test->getMyBlock();
        $firstBlockItem = $blockItems[0];
        $project = $firstBlockItem["project"]->getData();
        $geopoint = $test->getGeopoint();

        $languages = \Pimcore\Tool::getValidLanguages();
        \Pimcore\Model\DataObject\Localizedfield::setGetFallbackValues(false);
        $locale = 'de';
        $description = $test->getDescription($locale);

        $id = Link::getById(12);
        $link = $id->getHref();

        $structuredTable = $test->getTable();
        $rows = $structuredTable->getData();

        $class = ClassDefinition::getById(2);
        $fields = $class->getFieldDefinitions();

        foreach ($fields as $field) {
            $field->setLocked(true);
        }

        $class->save();


        $employee = DataObject\Task::getById(4);
        $employeeBrick = $employee->getTask();
        if ($employeeBrick === null) {
            throw $this->createNotFoundException('Employee not found');
        }

        $classificationStore = $test->getEmpstore();

        foreach ($classificationStore->getGroups() as $group) {
            $groupData = [
                'groupName' => $group->getConfiguration()->getName(),
                'keys' => []
            ];

            foreach ($group->getKeys() as $key) {
                $keyConfiguration = $key->getConfiguration();

                $value = $key->getValue();
                if ($value instanceof \Pimcore\Model\DataObject\Data\QuantityValue) {
                    $value = (string) $value;
                }

                $groupData['keys'][] = [
                    'id' => $keyConfiguration->getId(),
                    'name' => $keyConfiguration->getName(),
                    'value' => $value,
                    'isQuantityValue' => ($key->getFieldDefinition() instanceof QuantityValue),
                ];
            }

            $classificationStoreData[] = $groupData;
        }

    // Asset (Thumbnail)
        $asset = Asset::getById(20);

    // Tags
        $tag =  new \Pimcore\Model\Element\Tag();
        try {
            $tag->setName('Document')->save();
            \Pimcore\Model\Element\Tag::addTagToElement('object', 3, $tag);
        } catch (Exception $e) {
        }


    // Notes $Events
        $note = new Model\Element\Note();
        $note->setElement($test);
        $note->setDate(time());
        $note->setType("erp_import");
        $note->setTitle("changed availabilities to xyz");
        $note->setUser(1);

        $note->addData("myText", "text", "Some Text");
        $note->addData("myObject", "object", Model\DataObject::getById(3));
        $note->addData("myDocument", "document", Model\Document::getById(11));
        $note->addData("myAsset", "asset", Model\Asset::getById(20));

        $note->save();


        //create a new user for Employee
        $user = User::create([
            "parentId" => 0,
            "username" => "Faculty",
            "password" => "Faculty123",
            "hasCredentials" => true,
            "active" => true
        ]);
        $users = new Employee();
        $users->setUser($user->getId());


    //Adaptive Design Helper
        $device = DeviceDetector::getInstance();
        $device->getDevice(); // returns "phone", "tablet" or "desktop"
        if($device->isDesktop()){
            echo "I am " . $device;
        }elseif ($device->isTablet()){
            echo "Now I am " . $device;
        }else{
            echo "Then " . $device;
        }

    //Override Model
        $office = new \App\Model\DataObject\Employee();
        $office->setOffice('Calicut');

    // Parent class for objects
        //Override Model
        $parent = new \App\Model\DataObject\TestParent();
        $parent->setDes('I am the parent class for Employee');

        return $this->render('employee/employee.html.twig', [
            'project' => $project,
            'geopoint' => $geopoint,
            'description' => $description,
            'structuredTableData' => $rows,
            'classificationStoreData' => $classificationStoreData,
            'link'=>$link,
            'asset'=>$asset,
            'translated'=>$translated,
            'office'=>$office,
            'parent'=>$parent,
        ]);
    }

    /**
     * @Route("/iframe/summary")
     */
    public function summaryAction(Request $request): Response
    {
        $context = json_decode($request->get("context"), true);
        $objectId = $context["objectId"];

        $language = $context["language"] ?? "default_language";

        $object = Service::getElementFromSession('object', $objectId, '');

        if ($object === null) {
            $object = Service::getElementById('object', $objectId);
        }

        $response = '<h1>Title for language "' . $language . '": ' . $object->getData($language) . "</h1>";

        $response .= '<h2>Context</h2>';
        $response .= array_to_html_attribute_string($context);
        return new Response($response);
    }

    /**
     * @Route("/get-previous-version", name="get_previous_version")
     */
    public function getPreviousVersionAction(Request $request): Response
    {
        // Load the current object
        $currentObject = Employee::getById(3);

        if (!$currentObject) {
            // object with the given ID is not found
            throw $this->createNotFoundException('Object not found');
        }

        // Get the versions of the object
        $versions = $currentObject->getVersions();

        // Check if there are at least two versions (current and at least one previous)
        if (count($versions) >= 2) {
            // Get the previous version
            $previousVersion = $versions[count($versions) - 2];

            // Get the data of the previous version
            $previousObject = $previousVersion->getData();


            return new JsonResponse(['message' => 'Previous version retrieved successfully', 'data' => $previousObject]);
        } else {
            return new JsonResponse(['message' => 'No previous version available']);
        }
    }


}
