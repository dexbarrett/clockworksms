<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXmlElement;
use DexBarrett\ClockworkSms\Builders\Request;

class MessageRequest extends Request
{
    private $messageNodes = [
        'to' => 'To',
        'message' => 'Content',
        'from' => 'From',
        'long' => 'Long',
        'truncate' => 'Truncate',
        'invalidCharAction' => 'InvalidCharAction'
    ];

    public function build()
    {
        $messages = $this->command->getData();

        $xml = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8"?><body></body>');


        foreach ($messages as $message) {
            $smsNode = $xml->addChild('SMS');
            array_walk($message, function ($value, $key) use ($smsNode) {
                if (array_key_exists($key, $this->messageNodes)) {
                    $smsNode->addChild($this->messageNodes[$key], $value);
                }
            });
        }
        
        return $xml->children();
    }
}
