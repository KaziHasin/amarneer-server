<?php

namespace Modules\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    protected $fillable = ['plan_id', 'name'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
