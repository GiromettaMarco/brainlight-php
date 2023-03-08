# Brainlight PHP

Brainlight is a lightweight templating system with minimal logic pattern.

This is a PHP implementation of the Brainlight paradigm.

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Options](#options)
- [License](#license)

## Requirements

- PHP >= 8.1

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
