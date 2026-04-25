<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (int) $this->price,
            'duration_days' => (int) $this->duration_days,
            'contact_limit' => $this->contact_limit === null ? null : (int) $this->contact_limit,
            'features' => $this->whenLoaded('features', function () {
                return $this->features->pluck('name');
            }),
        ];
    }
}

