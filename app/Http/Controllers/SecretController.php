<?php

namespace App\Http\Controllers;

use App\Http\Resources\SecretResource;
use App\Models\Secret;
use App\Services\ResponseSerializer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecretController extends Controller
{
    private $serializer;
    private const XML_ROOT_NODE_NAME = 'secret';

    public function __construct(ResponseSerializer $responseSerializer) {
        $this->serializer = $responseSerializer;
    }

    public function store(Request $request)
    {
        if (!$this->isValid($request->all())) {
            return response('Invalid input', 405);
        }
        $secretText = $request->input('secret');
        $expireAfter = $request->input('expireAfter');

        $secret = new Secret();
        $secret->hash = Hash::make($secretText);
        $secret->secret_text = $secretText;
        $secret->remaining_views = $request->input('expireAfterViews');
        $secret->expires_at = $expireAfter > 0 ? Carbon::now()->addMinutes($expireAfter) : null;
        $secret->save();

        return $this->serializer->serialize(new SecretResource($secret), self::XML_ROOT_NODE_NAME);
    }

    private function isValid(array $data): bool
    {
        $validator = Validator::make($data, [
            'secret' => ['required', 'string'],
            'expireAfterViews' => ['required', 'integer', 'gt:0'],
            'expireAfter' => ['required', 'integer']
        ]);
        return !$validator->fails();
    }
}
