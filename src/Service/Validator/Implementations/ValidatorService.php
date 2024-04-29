<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\Validator\Implementations;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use IWD\SymfonyEntryContract\Exception\ValidatorException;
use IWD\SymfonyEntryContract\Service\Validator\Interfaces\ValidatorServiceInterface;

readonly class ValidatorService implements ValidatorServiceInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
    ) {
    }

    public function validate(object $object): void
    {
        /** @var ConstraintViolationList $violationList */
        $violationList = $this->validator->validate($object);
        $errors = [];
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        if ($violationList->count() !== 0) {
            $errorJson = $this->serializer->serialize($errors, 'json', [
                'json_encode_options' => JSON_UNESCAPED_UNICODE,
            ]);
            throw new ValidatorException($errorJson);
        }
    }
}
