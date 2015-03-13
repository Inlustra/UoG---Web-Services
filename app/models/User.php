<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

// each bear climbs many trees
    public function loginAttempts()
    {
        return $this->hasMany('Login');
    }

    public function validation()
    {
        return $this->hasOne('UserValidation');
    }

    /**
     * Get the roles a user has
     */
    public function roles()
    {
        return $this->belongsToMany('Role', 'user_roles');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isModerator()
    {
        return $this->hasRole('moderator');
    }

    public function canSticky()
    {
        return $this->isAdmin() || $this->isModerator();
    }


    /**
     * Find out if user has a specific role
     *
     * $return boolean
     */
    public function hasRole($check)
    {
        return in_array($check, array_fetch($this->roles->toArray(), 'name'));
    }

    /**
     * Add roles to user to make them a concierge
     */
    public function giveRole($title)
    {
        if (!$this->hasRole($title)) {
            $role = Role::where('name', '=', $title)->first();
            if (is_null($role)) {
                $role = new Role;
                $role->name = $title;
                $role->save();
            }
            $this->roles()->attach($role);
        }
    }
}
