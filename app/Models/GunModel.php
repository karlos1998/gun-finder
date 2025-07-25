<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GunModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'first_sync_completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_sync_completed' => 'boolean',
    ];

    /**
     * Get the user that owns the gun model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the listings for the gun model.
     */
    public function listings()
    {
        return $this->belongsToMany(Listing::class);
    }

    /**
     * Get the search URL for the gun model.
     */
    public function getSearchUrlAttribute(): string
    {
        return \App\Providers\ListingProvider\ListingProviderFactory::getProviderForGunModel($this)->getSearchUrl($this);
    }
}
