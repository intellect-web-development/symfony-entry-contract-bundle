<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Exception;

class SymfonyEntryContractBundleException extends \DomainException
{
    public function __construct(
        string $message = '',
        ?int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, (int) $code, $previous);
    }
}
