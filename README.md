# WikiDiki

WikiDiki uses [Wikipedia](https://wikipedia.org/) to translate words across multiple languages.

## Basic usage

WikiDiki usage is super simple.

```PHP
//instantiate
$wikidiki = new \maciejkrol\wikidiki\wikidiki ();
```

When translating you must tell WikiDiki what is the base language. English in this case. WikiDiki uses ISO two letter language codes.

This way you will get all the available translations as an associative array with the language codes as keys. 

```PHP
$word       =   'Tea';
$language   =   'en';

$translated = $wikidiki->translate($word, $language);   //array
```

You can also provide a third argument, a string with a single language code to get a single translated string.

```PHP
$word       =   'Tea';
$language   =   'en';
$to         =   'pl';

$translated = $wikidiki->translate($word, $language, $to);   //string
```

or an array to retrieve an array of translations.

```PHP
$word       =   'Tea';
$language   =   'en';
$to         =   ['pl', 'it'];

$translated = $wikidiki->translate($word, $language, $to);   //array
```

In case WikiDiki can't find a translation ``null`` will be returned;