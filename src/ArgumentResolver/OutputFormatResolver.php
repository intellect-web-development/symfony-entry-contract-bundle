<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\ArgumentResolver;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;

class OutputFormatResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        $format = (string) $request->attributes->get('_format', 'json');
        yield new OutputFormat($format);
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return OutputFormat::class === $argument->getType();
    }
}
