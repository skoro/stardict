# stardict
PHP interface to StarDict dictionaries

Install composer:
```
curl -sS https://getcomposer.org/installer | php
```

Install via composer:
```
php composer.phar require "skoro/stardict" "@dev"
```

## Usage

```php
require dirname(__FILE__) . '/vendor/autoload.php';

// Open dict's info, index and data.
$info = new skoro\stardict\Info('full-rus-eng.ifo');
$index = new skoro\stardict\Index($info);
$dict = new skoro\stardict\Dict($index);

// Lookup word.
print $dict->lookup('ведро');
```


## Command line utilities

There are two command line utilities (`vendor/bin`):
* `star2db` for convert dictionary to SQL database (which supported by PHP PDO).
* `starquery` for query words in dictionary.


## Convert dictionaries

Use `star2db` command line utility. For example, convert `dictionary` to SQLite database:
```
star2db sqlite:/my/db.sq3 dictionary.ifo
```
For more options see `star2db --help` output.

