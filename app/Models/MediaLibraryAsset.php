<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryAsset extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->acceptsFile(fn () => true)
            ->useDisk('public');

    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(5)
            ->nonQueued()
            ->performOnCollections('default');
    }
}
