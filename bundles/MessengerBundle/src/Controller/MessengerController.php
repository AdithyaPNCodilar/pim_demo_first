<?php

namespace MessengerBundle\Controller;

use MessengerBundle\Message\SmsNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerController extends AbstractController
{
    public function messageAction(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (isset($content['message'])) {
            $smsContent = $content['message'];

            $bus->dispatch(new SmsNotification($smsContent));

            return new JsonResponse(['status' => 'success', 'message' => 'SMS dispatched']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid request data'], 400);
    }
}
