<?php

namespace App\Controller;

use \Pimcore\Model\DataObject;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
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

    #[Template('employee/employee.html.twig')]
    public function employeeAction(Request $request): Response
    {
        $test = Employee::getById(3);
        $blockItems = $test->getMyBlock();
        $firstBlockItem = $blockItems[0];
        $project = $firstBlockItem["project"]->getData();
        $geopoint = $test->getGeopoint();

        $languages = \Pimcore\Tool::getValidLanguages();
        \Pimcore\Model\DataObject\Localizedfield::setGetFallbackValues(false);
        $locale = 'de';
        $description = $test->getDescription($locale);

        $structuredTable = $test->getTable();
        $rows = $structuredTable->getData();

        $d = Link::getById(12);
        echo($d->getHref());

        $class = DataObject\Employee::getById(2);
        $fields = $class->getFieldDefinitions();

        foreach ($fields as $field) {
            $field->setLocked(true);
        }

        $class->save();


        $employee = DataObject\Task::getById(4);
        $employeeBrick = $employee->getTask();
//        var_dump($employeeBrick);
//        die();
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

        return $this->render('employee/employee.html.twig', [
            'project' => $project,
            'geopoint' => $geopoint,
            'description' => $description,
            'structuredTableData' => $rows,
            'classificationStoreData' => $classificationStoreData,
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

}
