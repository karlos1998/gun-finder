<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Listing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gun_model_id',
        'listing_id',
        'title',
        'description',
        'price',
        'url',
        'image_url',
        'provider',
        'is_deleted',
        'phone_number',
        'city',
        'region',
        'condition',
        'gallery_images',
        'listing_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_deleted' => 'boolean',
        'gallery_images' => 'array',
        'listing_date' => 'date',
    ];

    /**
     * Get the gun models associated with the listing.
     */
    public function gunModels()
    {
        return $this->belongsToMany(GunModel::class);
    }

    /**
     * Get the full image URL with domain prefix.
     */
    public function getFullImageUrlAttribute(): ?string
    {
        if (!$this->image_url) {
            return null;
        }

        // Check if the URL already has a domain prefix
        if (str_starts_with($this->image_url, 'http')) {
            // For armybazar provider, route HTTP images through our proxy
            if ($this->provider === 'armybazar' && str_starts_with($this->image_url, 'http:')) {
                return route('image.proxy', ['url' => $this->image_url]);
            }
            return $this->image_url;
        }

        // Add the appropriate domain prefix based on the provider
        if ($this->provider === 'armybazar') {
            // For armybazar provider, route HTTP images through our proxy
            $fullUrl = 'http://www.armybazar.eu' . $this->image_url;
            return route('image.proxy', ['url' => $fullUrl]);
        }

        return 'https://www.netgun.pl' . $this->image_url;
    }

    /**
     * Get the full gallery image URLs with domain prefix.
     */
    public function getFullGalleryImagesAttribute(): array
    {
        if (!$this->gallery_images) {
            return [];
        }

        return array_map(function ($image) {
            // Check if the URL already has a domain prefix
            if (str_starts_with($image, 'http')) {
                // For armybazar provider, route HTTP images through our proxy
                if ($this->provider === 'armybazar' && str_starts_with($image, 'http:')) {
                    return route('image.proxy', ['url' => $image]);
                }
                return $image;
            }

            // Add the appropriate domain prefix based on the provider
            if ($this->provider === 'armybazar') {
                // For armybazar provider, route HTTP images through our proxy
                $fullUrl = 'http://www.armybazar.eu' . $image;
                return route('image.proxy', ['url' => $fullUrl]);
            }

            return 'https://www.netgun.pl' . $image;
        }, $this->gallery_images);
    }

    /**
     * Get the provider for this listing.
     */
    public function getProvider()
    {
        return \App\Providers\ListingProvider\ListingProviderFactory::getProvider($this->provider ?? 'netgun');
    }

}
