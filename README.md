EMSFormBundle
=============

Generate forms based on ElasticMS content configuration

[How to implement?](../master/Resources/doc/example.md)

Coding standards 
----------------
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

Build frontend resources
-------------

`````bash
npm install
npm run build
`````

In development stage 
`````bash
npm run start
`````

Documentation
-------------

[Installation](../master/Resources/doc/install.md)
[Config](../master/Resources/doc/config.md)