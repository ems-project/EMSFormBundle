# EMSFormBundle
Generate forms based on ElasticMS content configuration

## Coding standards 
PHP Code Sniffer is available via composer, the standard used is defined in phpcs.xml.diff:
````bash
composer phpcs
````

If your code is not compliant, you could try fixing it automatically:
````bash
composer phpcbf
````

PHPStan is run at level 7, you can check for errors locally using:
`````bash
composer phpstan
`````

PHP Mess Detector can generate a report in ./phpmd.html, rule violations are ignored by Travis for now.
````bash
composer phpmd
composer phpmd-win
````

Use phpmd-win when working on Windows!

## Build frontend resources
`````bash
npm install
npm run build
`````

In development stage 
`````bash
npm run start
`````

## Documentation

* [Installation](../master/Resources/doc/install.md)
* [Configuration](../master/Resources/doc/config.md)
* [How to implement?](../master/Resources/doc/example.md)
* [Handle Submitted data](../master/Resources/doc/handlers.md)
* [Supported fields](../master/Resources/doc/fields.md)
* [Supported validations](../master/Resources/doc/validations.md)
