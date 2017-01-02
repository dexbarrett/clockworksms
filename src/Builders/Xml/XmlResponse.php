<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXMLElement;
use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;

abstract class XmlResponse
{
    public function parse($responseData)
    {
        $xml = new SimpleXMLElement($responseData);

        $error = $xml->xpath('/*/ErrNo');
        $errorDesc = null;

        if (count($error) > 0) {
            $errorDesc = $xml->xpath('/*/ErrDesc');

            throw new ClockworkSmsException(
                (string)$errorDesc[0],
                (string)$error[0]
            );
        }

        return $this->parseResponse($xml);
    }

    abstract protected function parseResponse($data);
}
