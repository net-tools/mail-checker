# net-tools/mail-checker

## Composer library to check email existence through several webservices



## Setup instructions

To install net-tools/mailing package, just require it through composer : `require net-tools/mail-checker:^1.0.0`.




## How to use ?

This project makes it possible to check email existence through several webservices :

```php

// getting an API object of the desired webservice, and then creating checker object
$checker = new Checker(APIs\Bouncer::create('api_key_here'));

// do some checking stuff
if ( $checker->check('my_recipient@outlook.com'))
	echo "ok";
else
    echo "ko";

```


The APIs supported are : Bouncer (api key required), EmailValidator (api key required) and Trumail.io.





## API Reference

To read the entire API reference, please refer to the PHPDoc here :
https://nettools.ovh/api-reference/net-tools/namespaces/nettools-mailchecker.html



## PHPUnit

To test with PHPUnit, point the -c configuration option to the /phpunit.xml configuration file.

