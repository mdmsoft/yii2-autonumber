Auto Number Extension for Yii 2
========================

Entar gan ya.....


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
		[
			'class' => 'mdm\autonumber\Behavior',
			'attribute' => 'sales_num', // required
			'group' => 'sales', // required, unique
			'value' => 'SA-'.date('Y-m-d').'?' , // format auto number. '?' will be replaced with generated number
			'digit' => 4 // optional, default to null. 
		],
		...
  ];
}
```

Before it, prepare required table with execute yii migrate.
```
yii migrate --migrationPath=@mdm/autonumber/migrations
```