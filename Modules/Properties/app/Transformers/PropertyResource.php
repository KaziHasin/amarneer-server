<?php

namespace Modules\Properties\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'area' => $this->area,
            'location' => $this->location,
            'listing_type' => $this->listing_type,
            'description' => $this->description,
            'status' => $this->status,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'media' => $this->propertyGallery->map(function ($item) {
                return [
                    'type' => $item->type,
                    'url' => asset('storage/' . $item->file_path),
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
