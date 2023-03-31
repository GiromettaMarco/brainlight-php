# Brainlight PHP

Brainlight is a lightweight templating system with minimal logic pattern.

This is a PHP implementation of the Brainlight paradigm.

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Additional logic](#additional-logic)
- [Options](#options)
- [License](#license)

## Requirements

- PHP >= 8.1
- Composer

## Installation

Require Brainlight Php package in your composer project using the following command:

```
composer require brainlight/brainlight-php
```

## Usage

Create a new Brainlight Engine instance:

```php
use Brainlight\BrainlightPhp\Engine;

$engine = new Engine([
    'cacheDir' => __DIR__ . '/cache',
    'templatesDir' => __DIR__,
]);
```

Then render any Brainlight template:

```php
echo $engine->render('templateName', [
    'title' => 'Homepage',
]);
```

Make reference to the [Brainlight documentation](https://github.com/GiromettaMarco/brainlight) for templates syntax.

## Additional logic

Brainlight PHP supports templates with additional logic by making use of the ```Brainlight\BrainlightPhp\Logic``` class and namespaces.

First consider adding a namespace root in your engine configuration:

```php
use Brainlight\BrainlightPhp\Engine;

$engine = new Engine([
    'cacheDir' => __DIR__ . '/cache',
    'templatesDir' => __DIR__,
    'logicNamespace' => 'Logic',
]);
```

Then create a new logic class by extending ```Brainlight\BrainlightPhp\Logic```:

```php
namespace Logic;

use Brainlight\BrainlightPhp\Logic;

class Button extends Logic
{
    // ...
}
```

This way, while rendering ```{{ >+ button }}``` and ```{{ &+ button }}``` class ```Logic\Button``` will be loaded.

In addition, the template name inside these tags will be converted to a namespace. So ```buttons.button-delete``` will become ```Buttons\ButtonDelete```.

A Logic class must implement the ```getVariables()``` method:

```php
namespace Logic;

use Brainlight\BrainlightPhp\Logic;

class Button extends Logic
{
    protected function getVariables(array $parameters): array
    {
        return $parameters;
    }
}
```

The purpose of this function is to modify the arguments passed to the template. Such arguments are collected inside the ```parameters``` associative array.

It is possible to set mandatory arguments using the ```mandatory``` array property and mandatory slots using the ```mandatorySlots``` array property:

```php
namespace Logic\Inclusions;

use Brainlight\BrainlightPhp\Logic;

class Page extends Logic
{
    protected array $mandatory = [
        'title',
    ];

    protected array $mandatorySlots = [
        'content',
    ];

    // ...
}
```

It is also possible to change the default template associated with a logic class by setting its ```template``` property:

```php
namespace Logic;

use Brainlight\BrainlightPhp\Logic;

class Button extends Logic
{
    public ?string $template = 'buttons.custom-template';

    // ...
}
```

To render a template with additional logic directly from a PHP script, use the third parameter of the ```render()``` function:

```php
$engine->render('button', [], true);
```

## Options

The Engine constructor supports the following options:

### cacheDir

Type: string

Absolute path where cached templates will be stored.

This field is mandatory.

### templatesDir

Type: mixed

Absolute path to the directory containing templates. It also accepts an array of paths.

If ```false``` is provided, the ```render()``` method will accept a fully qualified filename instead of a template name.

Example:

```php
use Brainlight\BrainlightPhp\Engine;

$engine = new Engine([
    'cacheDir' => __DIR__ . '/cache',
    'templatesDir' => false,
]);

echo $engine->render(__DIR__ . '/templateName.brain');
```

Default value: ```false```

### partialsDir

Type: mixed

Absolute path to the directory containing partial templates for inclusions and extensions. It also accepts an array of paths.

If ```false``` is provided, the template engine will consider the template name of inclusion tags as a fully qualified filename instead of a template name.

If ```null``` is provided, partial templates will be resolved according to the same rule set with ```'templatesDir'```.

Default value: ```null```

### logicNamespace

Type: string|bool

Namespace root used to resolve additional logic classes.

Default value: ```false```

### extension

Type: string

The Brainlight template file extension.

Default value: ```'brain'```

### Escaping options

The following options (with default value) are applied to any escape operation performed with the PHP function [htmlspecialchars](https://www.php.net/manual/en/function.htmlspecialchars.php):

```php
[
    'escapeFlags' => ENT_QUOTES,
    'escapeEncoding' => 'UTF-8',
    'escapeDoubleEncode' => true,
]
```

## License

Brainlight PHP is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
