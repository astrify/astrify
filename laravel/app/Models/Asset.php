<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use App\Http\Resources\Asset as AssetResource;
use App\Models\Traits\BelongsToTeam;
use App\Models\Traits\HasOrganizations;

class Asset extends BaseModel implements HasMedia
{
    use BelongsToTeam, HasOrganizations, HasMediaTrait;

    protected $fillable = [
        'code',
        'account_id',
        'display_decimals',
        'name',
        'description',
        'details',
        'conditions',
        'redemption_instructions',
    ];

    public function registerMediaCollections()
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('logo')->singleFile();
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('images')
            ->fit(Manipulations::FIT_CROP, 1000, 750)
            ->performOnCollections('images');

        $this->addMediaConversion('logo')
            ->fit(Manipulations::FIT_CROP, 250, 250)
            ->performOnCollections('logo');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * @param  mixed
     * @return string
     */
    public function getLogoAttribute(): ?string
    {
        $url = $this->getFirstMediaUrl('logo', 'logo');
        if ($url) {
            return url($url);
        }

        return null;
    }

    /**
     * @return AssetResource
     */
    public function getResourceAttribute(): AssetResource
    {
        return new AssetResource($this);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * Asset->Account relationship
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * @param Builder $query
     * @param string $account_uuid
     * @return Builder
     */
    public function scopeAccountUuidFilter(Builder $query, string $account_uuid = null)
    {
        if (!empty($account)) {
            $query->whereHas('account', function ($query) use ($account_uuid) {
                $query->where('accounts.uuid', $account_uuid);
            });
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $code
     * @return Builder
     */
    public function scopeCodeFilter(Builder $query, $code)
    {
        if (!empty($code)) {
            $query->where('code', $code);
        }

        return $query;
    }
}
