<?php
namespace DexBarrett\ClockworkSms\Builders\Xml;

use SimpleXmlElement;

class BalanceResponse
{
    public function parse($data)
    {
        $xml = new SimpleXmlElement($data);
        $toArray = json_decode(json_encode($xml), true);
        $output = [];

        array_walk_recursive($toArray, function ($item, $key) use (&$output) {
            $output[$key] = (is_numeric($item))? floatval($item): $item;
        });

        return $output;
    }
}
