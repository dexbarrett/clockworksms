# ClockworkSms PHP Client
[![Build Status](https://travis-ci.org/dexbarrett/clockworksms.svg?branch=master)](https://travis-ci.org/dexbarrett/clockworksms)
[![License](https://poser.pugx.org/dexbarrett/clockworksms/license)](https://packagist.org/packages/dexbarrett/clockworksms)
[![StyleCI](https://styleci.io/repos/75573640/shield?branch=master)](https://styleci.io/repos/75573640)

Alternative PHP Client for sending text messages using the  [Clockwork SMS](https://www.clockworksms.com/) API.

*   [Requirements](#requirements)
*   [Installation](#installation)
*   [Usage](#usage)
      *   [Sending messages](#sending-messages)
      *   [Checking balance](#checking-balance)
      *   [Exception handling](#exception-handling)
* [Notes](#notes)

## Requirements
*   PHP >= 5.5
*   PHP's Curl extension (Guzzle is used to make the HTTP requests and it uses the curl extension by default)
*   A Clockwork API key

## Installation
Require the package through [Composer](https://getcomposer.org/download) by running this command in your project's root:
```
composer require dexbarrett/clockworksms
```

## Usage
For this first version I decided to keep the same API and return values that the original library. This may change in the future but for now it mimics theirs.

### Sending Messages

```php
use DexBarrett\ClockworkSms\ClockworkSms;

$clockworkSms = new ClockworkSms('yourAPIKeyHere');

// sending a single message
$result = $clockworkSms->send(['to' => '521234567890', 'message' => 'sms text here']);
```

If the message is successfully sent, you will receive back a multidimensional array similar to this:

```
array(1) {
  [0]=>
  array(3) {
    ["success"]=>
    bool(true)
    ["id"]=>
    string(12) "VE_427991229"
    ["sms"]=>
    array(1) {
      ["to"]=>
      string(12) "521234567890"
    }
  }
}
```
Each index of this array will contain an array as well with information about each sent message. The `success` key contains a boolean value which indicates if the message was sent correctly or not. If this is the case, the array will also contain an `id` key which represents a unique identifier generated by the Sms service. The `sms` key will contain information about the sent message like the number where it was sent to.

#### Multiple messages
To send multiple messages at once, simply pass a multidimensional array to the `send` method:

```php
$result = $clockworkSms->send([
    [ 'to' => '521111111111','message' => 'one message'],
    [ 'to' => '520000000000','message' => 'another message']
]);
```

In this case, the returned array will look something like this:

```
array(2) {
  [0]=>
  array(3) {
    ["success"]=>
    bool(true)
    ["id"]=>
    string(12) "VE_427991703"
    ["sms"]=>
    array(1) {
      ["to"]=>
      string(12) "521111111111"
    }
  }
  [1]=>
  array(3) {
    ["success"]=>
    bool(true)
    ["id"]=>
    string(12) "VE_427991704"
    ["sms"]=>
    array(1) {
      ["to"]=>
      string(12) "520000000000"
    }
  }
}

```
### Message errors
If you provide an invalid number for the `to` key in the messages array, what you'll receive back will contain an `error_code` and `error_message` key for each failed message. The `success` key will be `false` for that message:

```php
$result = $clockworkSms->send([
    [ 'to' => '520000000000','message' => 'one message'],
    [ 'to' => 'invalidNumber','message' => 'another message']
]);
```

```
array(2) {
  [0]=>
  array(3) {
    ["success"]=>
    bool(true)
    ["id"]=>
    string(12) "VE_427992069"
    ["sms"]=>
    array(1) {
      ["to"]=>
      string(12) "520000000000"
    }
  }
  [1]=>
  array(4) {
    ["success"]=>
    bool(false)
    ["error_code"]=>
    string(2) "10"
    ["error_message"]=>
    string(22) "Invalid 'To' Parameter"
    ["sms"]=>
    array(1) {
      ["to"]=>
      string(13) "invalidNumber"
    }
  }
}

```

### Checking Balance

You can also check how much credit you have in your Clockwork account by calling the `checkBalance` method:

``` php
$result = $clockworkSms->checkBalance();
```

This method will return an array like the following:
```
array(4) {
  ["account_type"]=>
  string(4) "PAYG"
  ["balance"]=>
  float(3.1)
  ["code"]=>
  string(3) "USD"
  ["symbol"]=>
  string(1) "$"
}
```

### Exception handling
There are cases where the library will throw an exception. Tipically if you provide an invalid API key or if you try to send messages but you don't have enough balance in your Clockwork account:

```php
use DexBarrett\ClockworkSms\ClockworkSms;
use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;

$clockworkSms = new ClockworkSms('invalidApiKey');

try{
    $result = $clockworkSms->send([
        [ 'to' => '520000000000','message' => 'one message'],
    ]);

} catch(ClockworkSmsException $e)
{
    echo $e->getMessage();
    // Invalid API Key, Insufficient Credits Available, or other error
}
```
## Notes

This packages works for the most common scenario which is sending messages by specifying the destination number and message content. It's in early stages so there are things from the original library that are not supported yet and others that were not included on purpose (like logging).

These are some of the features or improvements to make:
*   using options provided in the constructor (like specifying a 'from' address for messages or truncating long messages)
*   allowing to choose between HTTP and HTTPS (currently all is sent through the HTTP endpoints)
*   Laravel integration: creating a service provider for integration with the Laravel framework

## License
Developed by [Judas Borbón](https://jborbon.me) and released under the [MIT License](https://github.com/dexbarrett/clockworksms/blob/master/LICENSE.md)