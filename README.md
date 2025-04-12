Yii2 Timezone
=============
This component allows you to automatically determine the temporary zone of the user and set it up in the YII2 settings for the correct operation of temporary representations.

Installation
------------

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist asmoday74/yii2-timezone "*"
```

or add

```
"asmoday74/yii2-timezone": "*"
```

to the require section of your `composer.json` file.


Usage
-----

As soon as the component is installed, add the following config to the application settings (web.php for the template "basic", main.php for the "advanced" template):

```php
$config = [
    ...
    'bootstrap' => ['log', 'timezone'],
    ...
    'components' => [
        ...
        'timezone' => [
            'class' => asmoday74\timezone\UserTimezone::class,
            'controllerName' => 'timezone', //this is default name
            'defaultTimezone' => 'Europe/Moscow'
        ],
        ...
];
```


Additional information
-----

For the correct work with the time received from the database and saved in it, it is recommended to configure the time ward UTC when connecting to the database. For this: 
- In PostgreSQL:
```php
return [
	'class' => \yii\db\Connection::class,
	'dsn' => 'pgsql:host=localhost;dbname=dbname',
	'username' => 'user',
	'password' => 'password',
	'charset' => 'utf8',
	'on afterOpen' => function($event) {
	    $event->sender->createCommand("SET timezone TO 'UTC'")->execute();
	}
];

```
- In MySQL:
```php
return [
	'class' => \yii\db\Connection::class,
	'dsn' => 'mysql:host=localhost;dbname=dbname',
	'username' => 'user',
	'password' => 'password',
	'charset' => 'utf8',
	'on afterOpen' => function($event) {
	    $event->sender->createCommand("SET @@session.time_zone = '+00:00'")->execute();
	}
];

```