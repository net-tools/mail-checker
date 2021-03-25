# net-tools/mail-checker

## Composer library to check email existence through several webservices



## Setup instructions

To install net-tools/mailing package, just require it through composer : `require net-tools/mail-checker:^1.0.0`.




## How to use ?

This project makes it possible to check email existence through several webservices :

```php

// getting an API object of the desired webservice, and then creating checker object (api key and timeout as parameters)
$checker = new Checker(APIs\Bouncer::create('api_key_here', 6));

// do some checking stuff
if ( $checker->check('my_recipient@outlook.com'))
	echo "ok";
else
    echo "ko";

```


You can also upload an array of email addresses that the webservice will process (depending on the API used, it may answer with a task id to check later, either through a specific API call or by visiting the website)

```php

$api = APIs\Bouncer::create('api_key_here', 6);
$checker = new Checker($api);
$taskid = $checker->upload(['address1@me.com', 'otheraddress@here.com']);

// ...
// later check task processing status
if ( $api->status($taskid) )
	$json = $api->download($taskid);

```


The APIs supported are : Bouncer (api key required), EmailValidator (api key required) and Trumail.io.





## API Reference

To read the entire API reference, please refer to the PHPDoc here :
https://nettools.ovh/api-reference/net-tools/namespaces/nettools-mailchecker.html



## PHPUnit

To test with PHPUnit, point the -c configuration option to the /phpunit.xml configuration file.

