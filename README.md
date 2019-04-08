MobytServics Plugin
==================

### Usage

Use MobytServices to send SMS 
- SMS simple
- SMS with placeholder
- Check status of sent SMS


Basic usage
To send SMS simple:

```php
	...
    $sms = new SmsSimple(__LOGIN__, __PASSWORD__);
    $sms->setMessage('Hello World!');
    $sms->setRecipient('+336XXXXXXXX');
    $sms->send();
	...
```
### Todo

- Add unit test
- Add others features


### Installation

To send SMS with parameters:

```php
	...
    $sms = new SmsSimple(__LOGIN__, __PASSWORD__);
    $sms->setMessage('Hello ${name}!');
    $sms->setRecipient(['0' => ["recipient" => "+336XXXXXXXX", "name" => "Gaotian"]]);
    $sms->send();
	...
```

In app/Config/bootstrap.php:

either

```php
CakePlugin::loadAll()
``` 
or

```php
CakePlugin::loadAll([
	...
	'MobytServices' => [],
]);
```
or

```php
CakePlugin::load('MobytServices');
```

You must open accont on mobyt.fr to use it


### Requirements

CakePHP 2.0+

### License

Licensed under The MIT License
