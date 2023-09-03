<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\Factory\Implementations;

use IWD\SymfonyEntryContract\Exception\DeserializePayloadToInputContractException;
use IWD\SymfonyEntryContract\Exception\SymfonyEntryContractBundleException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use IWD\SymfonyEntryContract\Service\Factory\Interfaces\InputContractFactoryInterface;
use IWD\SymfonyEntryContract\Service\Validator\Interfaces\ValidatorServiceInterface;

class InputContractFactory implements InputContractFactoryInterface
{
    private readonly ValidatorServiceInterface $validator;
    private readonly SerializerInterface $serializer;

    public function __construct(
        ValidatorServiceInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string>                $payload
     *
     * @throws SymfonyEntryContractBundleException
     * @throws \JsonException
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface
    {
        if (!is_subclass_of($contractClass, InputContractInterface::class)) {
            throw new SymfonyEntryContractBundleException("{$contractClass} not is subclass of " . InputContractInterface::class, 400);
        }

        try {
            $inputContractDto = $this->serializer->deserialize(
                json_encode($payload, JSON_THROW_ON_ERROR),
                $contractClass,
                'json'
            );
        } catch (NotNormalizableValueException $notNormalizableValueException) {
            throw new DeserializePayloadToInputContractException(
                message: 'Not normalizable value. Check that required fields are passed and they are not null, and fields type.',
                code: 400,
                previous: $notNormalizableValueException,
                payload: $payload
            );
        }

        $this->validator->validate($inputContractDto);

        return $inputContractDto;
    }
}
