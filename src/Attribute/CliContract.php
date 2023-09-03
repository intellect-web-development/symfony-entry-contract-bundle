<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Attribute;

use Attribute;
use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class CliContract
{
    public function __construct(
        /** @var class-string<InputContractInterface> */
        public string $class
    ) {
    }
}
