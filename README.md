# StarDict
Provides PHP interface to StarDict dictionaries.

Install via composer:
```
composer require skoro/stardict
```

## Usage

```php
use StarDict\StarDict;

require dirname(__FILE__) . '/vendor/autoload.php';

$dict = StarDict::createFromFiles('dict.ifo', 'dict.idx', 'dict.dict.dz');

echo $dict->getDict()->getBookname(); // show dict name.

foreach ($dict->get('word') as $result) {
    echo $result->getValue();
}
```

## Caveats

- Only 2.4.2 StarDict version is supported.
- Option `sametypesequence` is required and cannot be empty.
