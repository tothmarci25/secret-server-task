<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SecretResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'hash' => $this->hash,
            'secretText' => $this->secret_text,
            'createdAt' => isset($this->created_at) ? $this->created_at->toJSON() : new MissingValue(),
            'expiresAt' => isset($this->expires_at) ? $this->expires_at->toJSON() : new MissingValue(),
            'remainingViews' => $this->remaining_views,
        ];
    }
}
