<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use IWD\SymfonyEntryContract\Service\RequestParser\Interfaces\RequestParserInterface;

class RequestParser implements RequestParserInterface
{
    /**
     * @return array<string, string>
     *
     * @throws \JsonException
     */
    public function parse(Request $request): array
    {
        $content = json_decode((string) $request->getContent(), true, 512);
        if (!is_array($content)) {
            $content = [];
        }

        /** @var array<string, string> $payload */
        $payload = [...$request->query->all(), ...$content, ...$request->request->all()];

        return $payload;
    }
}
