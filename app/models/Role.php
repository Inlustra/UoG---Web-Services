<?php

class Role extends Eloquent
{
    protected $table = 'roles';
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany('User', 'user_roles'); // this matches the Eloquent model
    }


}