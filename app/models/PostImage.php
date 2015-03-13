<?php

class PostImage extends Eloquent
{
    protected $hidden = array('user_id', 'created_at', 'updated_at','imageable_type', 'file_name', 'imageable_id');
    protected $table = 'images';
    public function imageable()
    {
        return $this->morphTo();
    }
// DEFINE RELATIONSHIPS --------------------------------------------------
// each bear HAS one fish to eat
    public function user()
    {
        return $this->belongsTo('User'); // this matches the Eloquent model
    }
}

