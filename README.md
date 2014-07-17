Auto Number Extension for Yii 2
========================

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

Prepare required table by execute yii migrate.
```
yii migrate --migrationPath=@mdm/autonumber/migrations
```
if wantn't use db migration. you can create required table manually.
```sql
CREATE TABLE auto_number (
    "group" varchar(32) NOT NULL,
    "number" int,
    optimistic_lock int,
    update_time int,
    PRIMARY KEY ("group")
);
```
Once the extension is installed, simply modify your ActiveRecord class:

```php
public function behaviors()
{
	return [
		[
			'class' => 'mdm\autonumber\Behavior',
			'attribute' => 'sales_num', // required
			'group' => $this->id_branch, // optional
			'value' => 'SA.'.date('Y-m-d').'.?' , // format auto number. '?' will be replaced with generated number
			'digit' => 4 // optional, default to null. 
		],
		...
	];
}

// it will set value $model->sales_num as 'SA.2014-06-25.0001'
```
Instead of behavior, you can use this extension as validator
```php
public function rules()
{
    return [
        [['sales_num'],'mdm\autonumber\NextValueValidator','format'=>'SA.'.date('Y-m-d').'.?'],
        ...
    ];
}
```