<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\RequestParser\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RequestParserInterface
{
    /**
     * @return array<string, string>
     *
     * @throws \JsonException
     */
    public function parse(Request $request): array;
}
