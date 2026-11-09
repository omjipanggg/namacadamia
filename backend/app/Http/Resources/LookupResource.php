<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LookupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->lookup_title,
            'subtitle' => $this->lookup_subtitle,
            'value' => $this->getKey(),
            'meta' => $this->lookup_meta,
        ];
    }
}
