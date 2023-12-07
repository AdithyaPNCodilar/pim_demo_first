<?php

namespace App\Controller;

use App\Service\NewlogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewlogController extends AbstractController
{
    /**
     * @Route("/new-log", name="new_log")
     */
    public function someAction(NewlogService $newlogService): JsonResponse
    {

        $newlogService->someMethod();

        return new JsonResponse(['message'=> 'Success']);

    }
}
