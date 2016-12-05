<?php
namespace DexBarrett\ClockworkSms;

use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;

class Client
{
    private $apiKey;

    private $base = 'api.clockworksms.com/xml';

    private $options = [
        'ssl' => false,
        'proxyHost' => null,
        'proxyPort' => null,
        'from' => null,
        'long' => null,
        'truncate' => null,
        'invalidCharAction' => null,
        'log' => false
    ];

    public function __construct($apiKey = null, array $options = [])
    {
        if ($apiKey === null) {
            throw new ClockworkSmsException('No API key provided');
        }

        $this->apiKey = $apiKey;

        $this->parseOptions($options);
    }

    public function getOptionValue($optionName)
    {
        $optionName = strtolower($optionName);

        if (! array_key_exists($optionName, $this->options)) {
            throw new ClockworkSmsException('Option does not exist');
        }

        return $this->options[$optionName];
    }

    protected function parseOptions(array $options)
    {
        $normalizedOptions = array_change_key_case($options, CASE_LOWER);

        $this->options = array_merge(
            $this->options,
            array_intersect_key($normalizedOptions, $this->options)
        );

        return $this->options;
    }
}
