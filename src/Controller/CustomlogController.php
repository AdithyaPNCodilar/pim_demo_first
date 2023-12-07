<?php

namespace App\Controller;

use App\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomlogController extends AbstractController
{
    /**
     * @Route("/custom-log", name="custom_log")
     */
    public function someAction(LogService $logService): JsonResponse
    {

        $logService->someMethod();

        return new JsonResponse(['message'=> 'Success']);

    }
}
