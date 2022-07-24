<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResponseSerializer
{
    protected Serializer $serializer;

    protected $mimeTypeMap = [
        'application/xml' => 'xml',
        'application/json' => 'json'
    ];

    protected $mimeType;


    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new JsonSerializableNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->mimeType = request()->prefers(array_keys($this->mimeTypeMap));
    }

    public function getMimeType()
    {
        return $this->mimeType ?: 'application/json';
    }

    public function serialize($value, $xmlRootNodeName = null)
    {
        $format = data_get($this->mimeTypeMap, $this->getMimeType(), 'json');
        return $this->serializer->serialize(
            $value,
            $format,
            isset($xmlRootNodeName) ? ['xml_root_node_name' => $xmlRootNodeName] : []
        );
    }


}
