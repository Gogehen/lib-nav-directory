<?php

namespace PhpSquad\DirectoryCreator\Models;

class Directory extends BaseModel
{
    public function children()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id');
    }

    public function lineage()
    {
        return $this->hasMany(Directory::class, 'parent_id', 'id')->with('children');
    }
}