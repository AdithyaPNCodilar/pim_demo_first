<?php

namespace MessengerBundle\MessageHandler;

use MessengerBundle\Message\SmsNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\HttpClient\HttpClient;
use MessengerBundle\Service\MessengerService;

#[AsMessageHandler]
class SmsNotificationHandler
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }
    public function __invoke(SmsNotification $message): void
    {
        $sms = $message->getContent();
        $this->messengerService->sendMessage($sms);
    }
}

