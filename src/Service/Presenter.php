<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;

class Presenter
{
    private readonly SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function present(
        mixed $data,
        array $headers = [],
        OutputFormat $outputFormat = null,
        int $status = 200,
    ): Response {
        if (null === $outputFormat) {
            $outputFormat = new OutputFormat('json');
        }

        $content = $this->serializer->serialize(
            $data,
            $outputFormat->getFormat()
        );

        $response = new Response(
            content: $content,
            status: $status
        );

        $headers = [...$headers, 'Content-Type' => 'application/' . $outputFormat->getFormat()];
        $response->headers->add($headers);

        return $response;
    }
}
