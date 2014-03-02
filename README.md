Auto Number Extension for Yii 2
========================

This extension provides a Web-based code generator, called Gii, for Yii 2 applications.
You can use Gii to quickly generate models, forms, modules, CRUD, etc.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mdmsoft/yii2-autonumber "*"
```

or add

```
"mdmsoft/yii2-autonumber": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your active record definision class:

```php
public function behavior()
{
  return [
	  'autonumber' => [
		  'class' => 'mdm\autonumber\Behavior',
		  ...
	  ],
	  ...
  ];
}
```
