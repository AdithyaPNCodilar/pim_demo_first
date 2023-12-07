<?php

namespace TrackBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TrackBundle\Model\AdminActivity\Listing;
use Symfony\Component\Yaml\Yaml;

class DefaultController extends FrontendController
{
    /**
     * @Route("/track")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from track');
    }

    /**
     * @Route("/tracked", name="track", methods={"GET"})
     */
    public function trackAction(Request $request): JsonResponse
    {
        $listing = new Listing();


        // Fetch data from the database
        $data = $listing->load();

        $totalRecords = count($data);

        $page = $request->query->get('page', 1);
        $pageSize = 100;
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($data, $offset, $pageSize);


        // Transform the data as needed
        $formattedData = [];
        foreach ($pagedData as $item) {
            $formattedData[] = [
                'id' => $item->getId(),
                'user_id' => $item->getUserId(),
                'action' => $item->getAction(),
                'timestamp' => $item->getTimestamp(),
            ];
        }


        // Return the data as JSON response
        return new JsonResponse([
            'total' => $totalRecords,
            'data' => $formattedData,
        ]);
    }
    /**
     * @Route("/save", name="save_system_data", methods={"POST"})
     */
    public function saveDataAction(Request $request): JsonResponse
    {
        $data = json_decode($request->get('data'), true);

        if ($data === null) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid data']);
        }

        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/TrackBundle/config/custom_settings/system_settings.yaml';

        $yamlData = Yaml::dump($data, 4);

        file_put_contents($configPath, $yamlData);

        return new JsonResponse(['success' => true, 'message' => 'Data saved successfully']);
    }

    /**
     * @Route("/load", name="load_system_data", methods={"GET"})
     */
    public function loadDataAction(): JsonResponse
    {
        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/TrackBundle/config/custom_settings/system_settings.yaml';

        // Load data from the YAML file
        $yamlData = file_get_contents($configPath);
        $data = Yaml::parse($yamlData);

        return new JsonResponse(['success' => true, 'data' => $data]);
    }

    /**
     * @Route("/yaml")
     */
    public function showdataAction(Request $request): Response
    {
        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/TrackBundle/config/custom_settings/system_settings.yaml';

        if (file_exists($configPath)) {
            $yamlData = file_get_contents($configPath);
            $data = Yaml::parse($yamlData);

            // Check if 'checked' is set to true in your YAML data
            $isChecked = isset($data['checked']) && $data['checked'] === true;

            if ($isChecked) {
                // Pass the parsed data to your Twig template
                return $this->render('@TrackBundle/show/show.html.twig', ['data' => $data]);
            } else {
                return new JsonResponse(['error' => 'Access denied. Please check your permissions.']);
            }

        } else {
            return new JsonResponse(['error' => 'YAML file not found']);
        }
    }
}
