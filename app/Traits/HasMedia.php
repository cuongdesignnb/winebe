<?php

namespace App\Traits;

use App\Models\MediaAsset;
use App\Models\MediaLink;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    public function mediaLinks(): MorphMany
    {
        return $this->morphMany(MediaLink::class, 'model')->orderBy('sort_order');
    }

    public function mediaAssets()
    {
        return $this->morphToMany(MediaAsset::class, 'model', 'media_links', 'model_id', 'asset_id')
            ->withPivot(['collection_name', 'field_name', 'sort_order', 'transformation_json'])
            ->withTimestamps()
            ->orderBy('media_links.sort_order');
    }

    public function attachMedia($assetIds, $collectionName = 'default')
    {
        $assetIds = (array) $assetIds;
        $syncData = [];
        foreach ($assetIds as $i => $id) {
            $syncData[$id] = [
                'collection_name' => $collectionName,
                'model_type' => static::class,
                'sort_order' => $i
            ];
        }

        // We use sync to avoid duplicates but it might remove others, 
        // a better approach is to delete old links for this collection and insert new ones
        $this->mediaLinks()->where('collection_name', $collectionName)->delete();

        foreach ($assetIds as $i => $id) {
            $this->mediaLinks()->create([
                'asset_id' => $id,
                'collection_name' => $collectionName,
                'sort_order' => $i
            ]);
        }
    }

    public function getFirstMediaUrl($collectionName = 'default')
    {
        $link = $this->mediaLinks()->where('collection_name', $collectionName)->with('asset')->first();
        return $link && $link->asset ? $link->asset->url : null;
    }

    public function getMedia($collectionName = 'default')
    {
        return $this->mediaAssets()->wherePivot('collection_name', $collectionName)->get();
    }
}
