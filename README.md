Yii widget based on Bootstrap Calendar
===

A Full view calendar based on Twitter Bootstrap. Please try the [demo](http://bootstrap-calendar.azurewebsites.net).

![Bootstrap full calendar](http://serhioromano.s3.amazonaws.com/github/bs-calendar.png)

### Features

- **Reusable** - there is no UI in this calendar. All buttons to switch view or load events are done separately. You will end up with your own uniquie calendar design.
- **Template based** - all view like **year**, **month**, **week** or **day** are based on templates. You can easily change how it looks or style it or even add new custom view.
- **LESS** - easy adjust and style your calendar with less variables file.
- **AJAX** - It uses AJAX to feed calendar with events. You provide URL and just return by this URL `JSON` list of events.
- **i18n** - language files are connected separately. You can easily translate the calendar into your own language. Holidays are also diplayed on the calendar according to your language

## Requirements

php >= 5.4
yii >= 1.1.14

### Install

	$ git submodule add https://github.com/femike/yii-bootstrap-calendar.git protected/extension/yii-bootstrap-calendar

### Quick setup

	config/main.php

```php

	'import' => [
		'ext.yii-bootstrap-calendar.*'
	],

```
### PHP example

```php

$this->widget('BootstrapCalendar', [
	'data' => $data,
	/** compliance, if in your model row name is different
	'compliance' => [
		'url' => ['name' => 'gid', 'value' => 'Yii::app()->createUrl("/action/view",["id" => $data->gid])'],
		'end' => ['name' => 'stop', 'value' => '$data->stop'],
	],
	*/
	'options' => [ // $('#calendar').calendar( options )
		'language' => 'ru-RU'
	],
	'buttons' => [ // if set buttons [ prev |today | next ] will show only setted buttons
		'today' => [
			'title' => 'Сегодня'
		],
		'next' => [
			'title' => 'Позже >>'
		]
	],
	'buttonsTime' => true, // show or hide buttons [ year | month | week | day ]
]);

```

