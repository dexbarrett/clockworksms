<?php
namespace DexBarrett\ClockworkSms\Serializers;

use DomDocument;
use SimpleXMLElement;
use DexBarrett\ClockworkSms\Commands\Command;
use DexBarrett\ClockworkSms\Encoders\Encoder;

class XmlSerializer extends Serializer
{
    protected $format = 'xml';

    public function serialize(Command $command)
    {
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $commandNode = $xml->createElement(ucfirst($command->getName()));
        $commandNode->appendChild($xml->createElement('Key', $command->getApiKey()));

        $requestBodyNodes = $this->builderFactory
                            ->createRequest($command, $this->getFormat())
                            ->build();

        if ($requestBodyNodes !== null) {
            foreach ($requestBodyNodes as $node) {
                $commandNode->appendChild(
                    $xml->importNode(dom_import_simplexml($node), true)
                );
            }
        }
       
        $xml->appendChild($commandNode);
        
        return $xml->saveXML();
    }

    public function deserialize(Command $command, $data)
    {
        return $this->builderFactory->parseResponse($command, $this->getFormat(), $data);
    }
}
