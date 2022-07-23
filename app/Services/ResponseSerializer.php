<?php

namespace App\Services;

use Illuminate\Http\Request;
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

    private $request;


    public function __construct(Request $request)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new JsonSerializableNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->request = $request;
    }

    public function serialize($value, $xmlRootNodeName = null)
    {
        $preferredMimeTye = $this->request->prefers(array_keys($this->mimeTypeMap));
        $format = isset($preferredMimeTye) ? data_get($this->mimeTypeMap, $preferredMimeTye, 'json') : 'json';
        return $this->serializer->serialize(
            $value,
            $format,
            isset($xmlRootNodeName) ? ['xml_root_node_name' => $xmlRootNodeName] : []
        );
    }


}
