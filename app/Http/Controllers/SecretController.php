<?php

namespace App\Http\Controllers;

use App\Http\Resources\SecretResource;
use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecretController extends Controller
{
    private const XML_ROOT_NODE_NAME = 'secret';

    public function store(Request $request)
    {
        if (!$this->isValid($request->all())) {
            return response('Invalid input', 405);
        }

        $secretText = $request->input('secret');
        $expireAfter = $request->input('expireAfter');

        $secret = new Secret();
        $secret->secret_text = $secretText;
        $secret->remaining_views = $request->input('expireAfterViews');
        $secret->expires_at = $expireAfter > 0 ? Carbon::now()->addMinutes($expireAfter) : null;
        $secret->save();

        return response()->serializeAsRequested(new SecretResource($secret), self::XML_ROOT_NODE_NAME);
    }

    public function show($hash)
    {
        $secret = Secret::findOrFail($hash);

        if ($secret->isExpired()) {
            $secret->delete();
            return response('Not found', 404);
        }

        $secret->remaining_views -= 1;
        $secret->save();

        return response()->serializeAsRequested(new SecretResource($secret), self::XML_ROOT_NODE_NAME);

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
