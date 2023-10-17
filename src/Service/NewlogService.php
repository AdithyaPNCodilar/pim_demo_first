<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class NewlogService
{
    private LoggerInterface $newlogLogger;

    public function __construct(LoggerInterface $newlogLogger)
    {
        $this->newlogLogger = $newlogLogger;
    }

    public function someMethod(): void
    {
        $this->newlogLogger->debug('New Log');
    }
}
