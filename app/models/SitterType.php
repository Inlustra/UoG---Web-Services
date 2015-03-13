<?php

class SitterType extends Eloquent
{  protected $hidden = array('pivot','color');
    protected $table = 'sitter_types';
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany('Post'); // this matches the Eloquent model
    }



}
