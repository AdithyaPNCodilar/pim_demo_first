<?php

namespace MessengerBundle\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MessengerService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function sendMessage( string $message): void
    {
        try {
            $response = $this->httpClient->request('POST', 'https://codilar-pimcore-test.free.beeceptor.com/dataobjects/test', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => $message,
                ],
            ]);
            $statusCode = $response->getStatusCode();
        } catch (\Exception $e) {
            // Handle exceptions
        } catch (TransportExceptionInterface $e) {
        }
    }
}
