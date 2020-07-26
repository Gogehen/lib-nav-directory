<?php

namespace PhpSquad\NavDirectory\Models;

class Directory extends BaseModel
{
    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id','id')->with('children');
    }
}