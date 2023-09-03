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

Command help:
```shell
php bin/console app:demo -h
```
Result:
```
Description:
  Demo CLI

Usage:
  app:demo [options]

Options:
      --email=EMAIL        User email [default: "user@dev.com"]
      --name=NAME          User name [default: "Foo"]
      --password=PASSWORD  Root user password [default: "bar"]
  -h, --help               Display help for the given command. When no command is given display help for the list command
  -q, --quiet              Do not output any message
  -V, --version            Display this application version
      --ansi|--no-ansi     Force (or disable --no-ansi) ANSI output
  -n, --no-interaction     Do not ask any interactive question
  -e, --env=ENV            The Environment name. [default: "dev"]
      --no-debug           Switch off debug mode.
  -v|vv|vvv, --verbose     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```