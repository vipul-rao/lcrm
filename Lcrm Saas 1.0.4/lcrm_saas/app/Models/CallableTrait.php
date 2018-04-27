<?php

namespace App\Models;

trait CallableTrait
{
    public function calls()
    {
        return $this->morphToMany(Call::class, 'callable');
    }
}