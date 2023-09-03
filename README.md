# Symfony Entry Contract bundle

## HTTP Entry:

InputContract:
```php
<?php

declare(strict_types=1);

namespace App\Entry\Http\Root;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    #[NotNull]
    #[Length(min: 3, max: 255)]
    public string $name;
}
```

Http Action/Controller:
```php
<?php

declare(strict_types=1);

namespace App\Entry\Http\Root;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Action
{
    #[Route(path: '', methods: ['GET'])]
    public function action(
        InputContract $inputContract
    ): Response {
        return new Response($inputContract->name);
    }
}
```

## CLI Entry:

InputContract:
```php
<?php

declare(strict_types=1);

namespace App\Entry\Console\Demo;

use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class InputContract implements InputContractInterface
{
    /** User email */
    #[NotNull]
    #[NotBlank]
    public string $email = 'user@dev.com';

    /** User name */
    #[NotNull]
    #[NotBlank]
    #[Length(min: 1)]
    public string $name = 'Foo';

    /** Root user password */
    #[NotNull]
    #[NotBlank]
    #[Length(min: 4)]
    public string $password = 'bar';
}
```

CliCommand:
```php
<?php

declare(strict_types=1);

namespace App\Entry\Console\Demo;

use IWD\SymfonyEntryContract\Attribute\CliContract;
use IWD\SymfonyEntryContract\Console\CliCommand;
use IWD\SymfonyEntryContract\Interfaces\InputContractInterface;
use IWD\SymfonyEntryContract\Service\CliContractResolver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:demo',
    description: 'Demo CLI',
)]
#[CliContract(class: InputContract::class)]
class DemoCommand extends CliCommand
{
    public function __construct(
        CliContractResolver $cliContractResolver,
    ) {
        parent::__construct($cliContractResolver);
    }

    /**
     * @param InputContract $inputContract
     */
    protected function handle(SymfonyStyle $io, InputContractInterface $inputContract): int
    {
        $io->success(
            sprintf(
                '%, %, %',
                $inputContract->name,
                $inputContract->email,
                $inputContract->password
            )
        );

        return self::SUCCESS;
    }
}
```