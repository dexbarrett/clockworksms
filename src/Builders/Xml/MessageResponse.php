<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXMLElement;

class MessageResponse extends XmlResponse
{
    protected $ignoredMessageKeys = ['errno', 'errdesc', 'messageid'];
    protected $smsKeys = ['To' => 'to'];

    public function parseResponse($xmlData)
    {
        $response = [];

        $messages = $xmlData->xpath('//SMS_Resp');

        foreach ($messages as $message) {
            $messageData = [];
            
            
            $errorNumber = $message->xpath('ErrNo');
            $errorDesc = $message->xpath('ErrDesc');
            $messageID = $message->xpath('MessageID');

            $messageData['success'] = (count($errorNumber) == 0);
            
            if (count($errorNumber) > 0) {
                $messageData['error_code'] = (string)$errorNumber[0];
            }

            if (count($errorDesc) > 0) {
                $messageData['error_message'] = (string)$errorDesc[0];
            }

            if (count($messageID) > 0) {
                $messageData['id'] = (string)$messageID[0];
            }


            $smsData = array_filter(
                (array)$message,
                [$this, 'isNotInIgnoredKeys'],
                ARRAY_FILTER_USE_BOTH
            );

            $messageData['sms'] = $this->replaceKeys($smsData);

            $response[] = $messageData;
        }

        return $response;
    }

    protected function isNotInIgnoredKeys($nodeValue, $nodeName)
    {
        return ! in_array(strtolower($nodeName), $this->ignoredMessageKeys);
    }

    protected function replaceKeys($smsData)
    {
        $newSmsData = [];

        foreach ($smsData as $key => $value) {
            $newSmsData[$this->getSmsKey($key)] = $value;
        }

        return $newSmsData;
    }

    protected function getSmsKey($key)
    {
        if (array_key_exists($key, $this->smsKeys)) {
            return $this->smsKeys[$key];
        }

        return $key;
    }
}
