<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Dto\Input;

use IWD\SymfonyEntryContract\Exception\SymfonyEntryContractBundleException;

class Locale
{
    /**
     * @param string[] $locales
     */
    public function __construct(
        public array $locales = []
    ) {
        if ($locales === []) {
            throw new SymfonyEntryContractBundleException('Locales is not set');
        }
    }

    public function getPriorityLang(): string
    {
        if ($this->locales === []) {
            return 'en';
        }

        return current($this->locales);
    }

    public function getAll(): array
    {
        return $this->locales;
    }
}
