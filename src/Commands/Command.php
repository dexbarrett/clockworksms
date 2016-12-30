<?php
namespace DexBarrett\ClockworkSms\Commands;

use DexBarrett\ClockworkSms\Builders\BuilderFactory;
use DexBarrett\ClockworkSms\Serializers\Serializer;
use DexBarrett\ClockworkSms\Serializers\XmlSerializer;

abstract class Command
{
    protected $apiKey;
    protected $command;
    protected $serializer;
    protected $data;

    public function __construct($apiKey, Serializer $serializer, array $data = [])
    {
        $this->apiKey = $apiKey;
        $this->data = $data;
        $this->serializer = $serializer;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getName()
    {
        return ucfirst($this->command);
    }

    public function getData()
    {
        return $this->data;
    }

    public function serialize()
    {
        return $this->serializer->serialize($this);
    }

    public function deserialize($responseData)
    {
        return $this->serializer->deserialize($this, $responseData);
    }
}
