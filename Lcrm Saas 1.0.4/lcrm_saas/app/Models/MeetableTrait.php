<?php


namespace App\Models;


trait MeetableTrait
{
    public function meetings()
    {
        return $this->morphToMany(Meeting::class, 'meetable');
    }
}