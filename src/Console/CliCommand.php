<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Console;

use Exception;
use IWD\SymfonyEntryContract\Attribute\CliContract;
use IWD\SymfonyEntryContract\Exception\ValidatorException;
use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use IWD\SymfonyEntryContract\Service\CliContractResolver;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class CliCommand extends Command
{
    protected SymfonyStyle $io;
    protected InputInterface $input;
    protected OutputInterface $output;

    public function __construct(
        private readonly CliContractResolver $cliContractResolver,
    ) {
        parent::__construct();
    }

    /**
     * @description You can override this method and return your target class here, or use the CliContract attribute.
     *
     * @return class-string<InputContractInterface>
     * @throws Exception
     */
    protected function getInputContractClass(): string
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            if (CliContract::class === $attribute->getName()) {
                $class = (string) ($attribute->getArguments()['class'] ?? NullInputContract::class);
                if (!class_exists($class)) {
                    throw new Exception(
                        sprintf(
                            '"%s" class not exists, check "%s" argument',
                            $class,
                            CliContract::class,
                        )
                    );
                }
                if (!is_subclass_of($class, InputContractInterface::class)) {
                    throw new Exception(
                        sprintf(
                            '"%s" is not subclass of "%s"',
                            $class,
                            InputContractInterface::class,
                        )
                    );
                }

                return $class;
            }
        }

        return NullInputContract::class;
    }

    protected function configure(): void
    {
        if ($this->autoconfigure()) {
            $inputContractClass = $this->getInputContractClass();
            $inputContract = new $inputContractClass();
            // Получаем объект ReflectionClass для класса InputContract
            $reflectionClass = new ReflectionClass($inputContractClass);

            // Получаем свойства класса
            $properties = $reflectionClass->getProperties();

            // Массив для хранения информации о свойствах
            $propertiesInfo = [];

            // Обходим каждое свойство
            foreach ($properties as $property) {
                // Получаем имя свойства
                $propertyName = $property->getName();

                // Получаем комментарий свойства
                $propertyCommentRaw = $property->getDocComment();
                if (false === $propertyCommentRaw) {
                    $propertyCommentRaw = '';
                }
                $propertyComment = trim(str_replace(['/**', '*/', '/*'], '', $propertyCommentRaw));

                // Проверяем, является ли свойство nullable
                $isNullable = $property->getType()?->allowsNull();

                // Получаем дефолтное значение свойства
                $propertyDefaultValue = $property->isInitialized($inputContract) ? $property->getValue($inputContract) : null;

                // Добавляем информацию о свойстве в массив
                $propertiesInfo[$propertyName] = [
                    'name' => $propertyName,
                    'description' => $propertyComment,
                    'nullable' => $isNullable,
                    'default' => $propertyDefaultValue,
                ];
            }

            foreach ($propertiesInfo as $propertyInfo) {
                $this->addOption(
                    name: $propertyInfo['name'],
                    mode: $propertyInfo['nullable'] ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_REQUIRED,
                    description: $propertyInfo['description'],
                    default: $propertyInfo['default'],
                );
            }
        }
    }

    abstract protected function handle(InputContractInterface $inputContract): int;

    protected function autoconfigure(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->input = $input;
        $this->output = $output;

        try {
            $inputContractClass = $this->getInputContractClass();
            /** @var InputContractInterface $inputContract */
            $inputContract = $this->cliContractResolver->resolve($input, $inputContractClass);
        } catch (ValidatorException $validatorException) {
            $violations = json_decode($validatorException->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            $message = 'Command options has violations:' . PHP_EOL;

            $i = 0;
            foreach ($violations as $property => $violation) {
                ++$i;
                $message .= sprintf(
                    '%s. %s: %s',
                    $i,
                    (string) $property,
                    (string) $violation,
                ) . PHP_EOL;
            }
            $this->io->error($message);

            return self::INVALID;
        }

        return $this->handle(
            inputContract: $inputContract,
        );
    }
}
