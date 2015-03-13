<?php

class Post extends Eloquent
{

    protected $table = 'posts';

    // DEFINE RELATIONSHIPS --------------------------------------------------
    // each bear HAS one fish to eat
    public function user()
    {
        return $this->belongsTo('User'); // this matches the Eloquent model
    }

    public function sitterTypes()
    {
        return $this->belongsToMany('SitterType', 'post_sitter_types');
    }

    public function images()
    {
        return $this->morphMany('PostImage', 'imageable');
    }

    public function delete()
    {
        $this->sitterTypes()->detach();
        // delete all related photos
        $this->images()->delete();
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // Photo::where("user_id", $this->id)->delete()
        // delete the user
        return parent::delete();
    }

    public function scopePinned($query)
    {
        return $query->where('pinned', '=', '1');
    }

    public function scopeContent($query, $content)
    {
        return $query->where('content', 'like', '%'.$content.'%');
    }

    public function scopeTitle($query, $title)
    {
        return $query->where('title', 'like', '%'.$title.'%');
    }

    public function scopeLocation($query, $title)
    {
        return $query->where('location', 'like', '%'.$title.'%');
    }

    public function scopeNotpinned($query)
    {
        return $query->where('pinned', '=', '0');
    }

    public function scopeOfType($query, $against)
    {
        return $query->whereHas('sitterTypes', function ($q) use ($against) {
            $q->whereIn('id', $against);
        });
    }
    public function scopeLikeType($query, $against)
    {
        return $query->whereHas('sitterTypes', function ($q) use ($against) {
            $q->where('name', 'LIKE','%'.$against.'%');
        });
    }

    public function scopeByUser($query, $userid)
    {
        return $query->where('user_id', '=', $userid);
    }

    public function scopeWithImages($query)
    {
        return $query->has('images');
    }

    public function scopeWithoutImages($query)
    {
        return $query->has('images', '<=', 0);
    }

    public function scopeDistance($query, $lat, $long)
    {
        return $query->selectRaw('*, SQRT(POW(69.1 * (lat - ' . $lat . '), 2) +
        POW(69.1 * (' . $long . '- lon) * COS(lat / 57.3), 2)) AS distance')->orderBy('distance');
    }

}
