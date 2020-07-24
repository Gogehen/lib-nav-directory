<?php

namespace PhpSquad\NavDirectory\Models;

class Directory extends BaseModel
{
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id','id')->with('children');
    }
}