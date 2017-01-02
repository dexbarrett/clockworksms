<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXMLElement;

class MessageResponse extends XmlResponse
{
    protected $errorNodes = ['errno', 'errdesc'];

    public function parseResponse($xmlData)
    {
        $response = [];

        $messages = $xmlData->xpath('//SMS_Resp');

        foreach ($messages as $message) {
            $messageData = [];
            
            
            $errorNumber = $message->xpath('ErrNo');
            $errorDesc = $message->xpath('ErrDesc');

            $messageData['success'] = (count($errorNumber) == 0);

            if (count($errorNumber) > 0) {
                $messageData['errorCode'] = (string)$errorNumber[0];
            }

            if (count($errorDesc) > 0) {
                $messageData['errorDesc'] = (string)$errorDesc[0];
            }


            $messageData['sms'] = array_filter(
                (array)$message,
                [$this, 'isNotErrorNode'],
                ARRAY_FILTER_USE_BOTH 
            );

            $response[] = $messageData;

        }

    }

    protected function isNotErrorNode($nodeValue, $nodeName)
    {
        return ! in_array(strtolower($nodeName), $this->errorNodes);
    }
}
