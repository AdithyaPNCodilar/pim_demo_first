<?php

namespace TrackBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TrackBundle\Model\AdminActivity\Listing;

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
        foreach ($data as $item) {
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
}

