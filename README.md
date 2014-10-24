Web Page Metadata
=================
DB based web page metadata for SEO performance annoying free development.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ptheofan/yii2-meta "*"
```

or add

```
"ptheofan/yii2-meta": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Basic Usage
In your components configuration add the following
```php
'meta' => [
    'class' => 'ptheofan\meta\Meta',
]
```

run the migration by running
```
./yii migrate --migrationPath="@vendor/ptheofan/yii2-meta/migrations"
```

In your code, if you want to automatically set the metadata for a page call
```php
Yii::$app->meta->setMeta();
```
This will automatically load the correct row from the database using the currently running
route (module/controller/action or controller/action).
It will automatically identify and set the canonical, the og:url. The reset depend on the database entry,
the argument used and the defaults as identified in your component config.

You can optionally override data by specifying them in a parameter array
```
Yii::$app->meta->setMeta(['title' => 'My cool override']);
```php

or you can use defaults to be used throughout the site in the component config
```php
'meta' => [
    'class' => 'ptheofan\meta\Meta',
    'defaults' => [
        'og:type' => 'website',
        'author' => 'My Cool Company',
    ],
]
```

The defaults will always override any values passed in through parameter or through the database.