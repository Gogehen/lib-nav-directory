<?php

namespace PhpSquad\NavDirectory\Repositories;

use PhpSquad\NavDirectory\Models\Directory;

class DirectoryRepository
{
    public function create(string $accountId, string $userId, ?string $parentId, string $type, string $name, string $icon): Directory
    {
        $parentId = $parentId ? $parentId : 'base_nav_element';

        $dir = new Directory();
        $dir->account_id = $accountId;
        $dir->user_id = $userId;
        $dir->parent_id = $parentId;
        $dir->icon = $icon;
        $dir->type = $type;
        $dir->name =$name;
        $dir->save();

        return $dir;
    }

    public function list(string $accountId, string $rootId)
    {
        return Directory::where('account_id', '=', $accountId)
            ->where('parent_id', '=', $rootId)
            ->with('children')
            ->get();
    }

    public function update(string $id, string $accountId, string $userId, ?string $parentId, string $type, string $name, string $icon)
    {
        $dir = Directory::find($id);
        $dir->account_id = $accountId;
        $dir->user_id = $userId;
        $dir->parent_id = $parentId;
        $dir->type = $type;
        $dir->name = $name;
        $dir->icon = $icon;
        $dir->save();

        return $dir;

    }
}