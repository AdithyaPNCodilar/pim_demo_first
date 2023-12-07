<?php

namespace TrackBundle\Controller\Api;

use Pimcore\Controller\FrontendController;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\General;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TrackBundle\Service\ApiService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends FrontendController
{
    private LoggerInterface $logger;
    private ApiService $apiService;

    private mixed $eventDispatcher;

    public function __construct(LoggerInterface $logger, ApiService $apiService)
    {
        $this->logger = $logger;
        $this->apiService = $apiService;

    }

    public function getDataObject(Request $request, $id): JsonResponse
    {
        $apiKey = $request->headers->get('Authorization');

        if ($this->apiService->authenticate($apiKey)) {
            $dataObject = General::getById($id);

            if ($dataObject instanceof General) {
                $data = [
                    'name' => $dataObject->getName(),
                    'email' => $dataObject->getEmail(),
                ];
                return new JsonResponse($data);
            } else {
                return new JsonResponse(['error' => 'Data object not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function createDataObject(Request $request, $name, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $apikey = $request->headers->get('Authorization');

        $this->logger->info('API Key received for createDataObject', ['api_key' => $apikey]);

        $dataObject = new General();
        $dataObject->setKey($name);
        $dataObject->setParentId(1);

        $requestData = json_decode($request->getContent(), true);

        foreach ($requestData as $property => $value) {
            if (property_exists(General::class, $property)) {
                $setterMethod = 'set' . ucfirst($property);

                if (method_exists($dataObject, $setterMethod)) {
                    $dataObject->$setterMethod($value);
                }
            }
        }

        try {
            $dataObject->save();

            if ($dataObject->getId()) {
                $event = new DataObjectEvent($dataObject);
                $eventDispatcher->dispatch($event, "pimcore.dataobject.postUpdate");

                $requestMethod = $request->getMethod();

                return new JsonResponse(['message' => 'created successfully', 'id' => $dataObject->getId(), 'messages' => "You hit a $requestMethod request"]);
            } else {
                return $this->json(['error' => 'Error creating Custom object'], 500);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error creating Custom object: ' . $e->getMessage()], 500);
        }
    }
}
