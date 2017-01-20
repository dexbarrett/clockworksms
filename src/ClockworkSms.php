<?php
namespace DexBarrett\ClockworkSms;

use DexBarrett\ClockworkSms\Commands\CommandFactory;
use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;
use GuzzleHttp\Client as GuzzleClient;

class ClockworkSms
{
    const MESSAGE_LIMIT = 500;

    private $apiKey;
    private $httpClient;
    private $commandFactory;
    private $format = 'xml';

    private $base = 'api.clockworksms.com';

    private $contentTypes = [
        'xml' => 'text/xml'
    ];

    private $options = [
        'ssl' => true,
        'from' => null,
        'long' => null,
        'truncate' => null,
        'invalidCharAction' => null,
    ];

    private $requiredParams = [
        'send' => ['to', 'message']
    ];

    private $validParams = [
        'send' => ['to', 'message', 'from', 'long', 'truncate', 'invalidCharAction']
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

    public function send(array $messages)
    {
        $messages = ($this->containsMultipleMessages($messages))? $messages : [$messages];

        $this->validateMessages($messages);

        return $this->sendRequest('send', $this->mergeMessageOptions($messages));
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
        $requestUrl = $this->buildApiEndpointUrl($endpoint);

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

    private function containsMultipleMessages($messages)
    {
        return count(array_filter($messages, 'is_array')) > 0;
    }

    protected function buildApiEndpointUrl($endpoint)
    {
        return sprintf(
            '%s://%s/%s/%s',
            ($this->getOptionValue('ssl'))? 'https':'http',
            $this->base,
            $this->format,
            $endpoint
        );
    }

    protected function validateParameters($method, $parameters)
    {
        $missingParameters = array_diff(
            $this->requiredParams[$method],
            array_keys($parameters)
        );

        if (count($missingParameters)) {
            throw new ClockworkSmsException(
                "the following parameters are missing in call to method '{$method}': "
                . implode(', ', $missingParameters)
            );
        }
    }

    protected function validateMessages($messages)
    {
        if ($this->exceedsMessageLimit($messages)) {
            throw new ClockworkSmsException(sprintf(
                'Please call the send method with a maximum of %d messages',
                self::MESSAGE_LIMIT
            ));
        }

        foreach ($messages as $message) {
            $this->validateParameters('send', $message);
        }
    }

    protected function exceedsMessageLimit($messages)
    {
        return count($messages) > self::MESSAGE_LIMIT;
    }

    protected function mergeMessageOptions(array $messages)
    {
        $mergedMessages = [];

        $globalMessageOptions = array_filter(
            $this->options,
            function ($value, $key) {
                return $key != 'ssl';
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($messages as $message) {
            $validParams = array_intersect_key(
                $message,
                array_flip($this->validParams['send'])
            );

            $mergedMessages[] = array_filter(
                array_merge($globalMessageOptions, $validParams),
                function ($value) {
                    return $value !== null;
                }
            );
        }

        return $mergedMessages;
    }
}
