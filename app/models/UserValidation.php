<?php

class UserValidation extends Eloquent
{
    protected $table = 'user_validations';

    // DEFINE RELATIONSHIPS --------------------------------------------------
    // each bear HAS one fish to eat
    public function user()
    {
        return $this->belongsTo('user_validations'); // this matches the Eloquent model
    }



}
