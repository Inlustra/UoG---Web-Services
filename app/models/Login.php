<?php

class Login extends Eloquent
{
    protected $table = 'logins';

    // DEFINE RELATIONSHIPS --------------------------------------------------
    // each bear HAS one fish to eat
    public function user()
    {
        return $this->belongsTo('User'); // this matches the Eloquent model
    }



}
