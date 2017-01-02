<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXmlElement;
use DexBarrett\ClockworkSms\Builders\Request;

class MessageRequest extends Request
{
    public function build()
    {
        $messages = $this->command->getData();

        $xml = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8"?><body></body>');


        foreach ($messages as $message) {
            $smsNode = $xml->addChild('SMS');
            $smsNode->addChild('To', $message['to']);
            $smsNode->addChild('Content', $message['message']);
        }

        return $xml->children();
    }
}
