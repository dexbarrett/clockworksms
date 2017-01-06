<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXmlElement;

class BalanceResponse extends XmlResponse
{
    protected $keys = [
        'AccountType' => 'account_type',
        'Balance' => 'balance',
        'Code' => 'code',
        'Symbol' => 'symbol'
    ];

    protected function parseResponse($xml)
    {
        $toArray = json_decode(json_encode($xml), true);
        $output = [];

        array_walk_recursive($toArray, function ($item, $key) use (&$output) {
            $key = $this->replaceKey($key);
            $output[$key] = (is_numeric($item))? floatval($item): $item;
        });


        return $output;
    }

    protected function replaceKey($key)
    {
        if (array_key_exists($key, $this->keys)) {
            return $this->keys[$key];
        }

        return $key;
    }
}
