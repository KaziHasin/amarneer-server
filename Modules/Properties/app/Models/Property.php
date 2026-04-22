<?php

namespace Modules\Properties\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'category_id', 'name', 'slug', 'description', 'price', 'area', 'location', 'listing_type', 'status', 'is_featured'];

    protected static function booted(): void
    {
        static::creating(function (self $property) {
            if (blank($property->slug) && filled($property->name)) {
                $property->slug = self::generateUniqueSlug($property->name);
            }
        });

        static::updating(function (self $property) {
            // If name changed and slug wasn't explicitly set, keep slug in sync.
            if (
                $property->isDirty('name')
                && !$property->isDirty('slug')
                && filled($property->name)
            ) {
                $property->slug = self::generateUniqueSlug($property->name, $property->getKey());
            }

            // If slug is manually blanked out, regenerate it.
            if (blank($property->slug) && filled($property->name)) {
                $property->slug = self::generateUniqueSlug($property->name, $property->getKey());
            }
        });
    }

    protected static function generateUniqueSlug(string $name, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : Str::random(8);

        $i = 1;
        while (
            static::query()
                ->when($ignoreId !== null, fn($q) => $q->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $i++;
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

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
