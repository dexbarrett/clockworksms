<?php
namespace DexBarrett\ClockworkSms;

use DexBarrett\ClockworkSms\Commands\CommandFactory;
use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private $apiKey;
    private $httpClient;
    private $commandFactory;
    private $format = 'xml';

    private $base = 'http://api.clockworksms.com';

    private $contentTypes = [
        'xml' => 'text/xml'
    ];

    private $options = [
        'ssl' => false,
        'proxyHost' => null,
        'proxyPort' => null,
        'from' => null,
        'long' => null,
        'truncate' => null,
        'invalidCharAction' => null,
    ];

    public function __construct(
        $apiKey = null,
        array $options = [],
        GuzzleClient $httpClient = null,
        CommandFactory $commandFactory = null
    ) {
    
        if ($apiKey === null) {
            throw new ClockworkSmsException('No API key provided');
        }
    
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient ?: new GuzzleClient;
        $this->commandFactory = $commandFactory ?: new CommandFactory;
        
        $this->parseOptions($options);
    }

    public function checkBalance()
    {
        return $this->sendRequest('balance');
    }

    public function send(array $message)
    {
        return $this->sendRequest('send', $message);
    }

    public function getOptionValue($optionName)
    {
        $optionName = strtolower($optionName);

        if (! array_key_exists($optionName, $this->options)) {
            throw new ClockworkSmsException('Option does not exist');
        }

        return $this->options[$optionName];
    }

    protected function sendRequest($endpoint, $data = [])
    {
        
        $requestUrl = "{$this->base}/{$this->format}/{$endpoint}";

        $command = $this->commandFactory->createCommand(
            $endpoint,
            $this->apiKey,
            $this->getFormat(),
            $data
        );

        $response = $this->httpClient->request('POST', $requestUrl, [
            'headers' => [
                'Content-Type' => $this->getContentType()
            ],
            'body' => $command->serialize()
        ]);


        return $command->deserialize((string)$response->getBody());
    }


    protected function parseOptions(array $options)
    {
        $normalizedOptions = array_change_key_case($options, CASE_LOWER);

        $this->options = array_merge(
            $this->options,
            array_intersect_key($normalizedOptions, $this->options)
        );
    }

    protected function getContentType()
    {
        return $this->contentTypes[$this->format];
    }

    protected function getFormat()
    {
        return $this->format;
    }
}
