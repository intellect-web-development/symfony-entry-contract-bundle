<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\Validator\Interfaces;

use IWD\SymfonyEntryContract\Exception\ValidatorException;

interface ValidatorServiceInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(object $object): void;
}
