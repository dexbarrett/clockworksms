<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXmlElement;
use DexBarrett\ClockworkSms\Builders\Request;

class MessageRequest extends Request
{
    public function build()
    {
        $xml = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8"?><SMS></SMS>');

        $xml->addChild('To', $this->command->getData()['to']);
        $xml->addChild('Content', $this->command->getData()['message']);

        return $xml;
    }
}
