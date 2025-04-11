<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    // ne fonctionne pas pour une collection, remplace data par property
    public static $wrap = 'property';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->when(true, $this->price),
            'options' => OptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
