<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;
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

    protected $requestedMimeType;


    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new JsonSerializableNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->requestedMimeType = request()->prefers(array_keys($this->mimeTypeMap));
    }

    public function getRequestedMimeType(): ?string
    {
        return $this->requestedMimeType;
    }


    /**
     * @throws HttpException
     */
    public function serialize($value, $xmlRootNodeName = null): string
    {
        if (!isset($this->requestedMimeType)) {
            throw new HttpException(406, 'Requested content type not supported');
        }
        $format = $this->mimeTypeMap[$this->requestedMimeType];
        return $this->serializer->serialize(
            $value,
            $format,
            isset($xmlRootNodeName) ? ['xml_root_node_name' => $xmlRootNodeName] : []
        );
    }


}
