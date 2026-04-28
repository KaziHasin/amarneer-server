<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'icon', 'category_id'];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_amenity');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
