<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use IWD\SymfonyEntryContract\Dto\Input\Locale;

interface LocaleMakerInterface
{
    public static function make(Request $request): Locale;
}
