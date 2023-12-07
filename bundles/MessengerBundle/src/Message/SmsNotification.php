<?php

namespace MessengerBundle\Message;

class SmsNotification
{
    public function __construct(private string $content) {
    }

    public function getContent(): string
    {
    return $this->content;
    }
}
