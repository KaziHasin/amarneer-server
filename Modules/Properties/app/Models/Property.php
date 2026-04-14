<?php

namespace Modules\Properties\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'category_id', 'name', 'slug', 'description', 'price', 'area', 'location', 'listing_type', 'status', 'is_featured'];


    /**
     * Summary of category
     * @return BelongsTo<Category, Property>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    /**
     * Summary of propertyGallery
     * @return HasMany<PropertyGallery, Property>
     */
    public function propertyGallery(): HasMany
    {
        return $this->hasMany(PropertyGallery::class);
    }

    /**
     * Summary of user
     * @return BelongsTo<User, Property>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
