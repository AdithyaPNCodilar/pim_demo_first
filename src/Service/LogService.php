<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class LogService
{
    private LoggerInterface $customlogLogger;

    public function __construct(LoggerInterface $customlogLogger)
    {
        $this->customlogLogger = $customlogLogger;
    }

    public function someMethod(): void
    {
        $this->customlogLogger->debug('Test Message');
    }
}
