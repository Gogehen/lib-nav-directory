<?php

namespace PhpSquad\NavDirectory\Models;

class Directory extends BaseModel
{
    public function projects()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id');
    }

    public function folders()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id')->with('children');
    }
}