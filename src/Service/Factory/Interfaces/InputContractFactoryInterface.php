<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\Factory\Interfaces;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;

interface InputContractFactoryInterface
{
    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string>                $payload
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface;
}
