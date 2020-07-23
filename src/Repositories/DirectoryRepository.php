<?php


namespace PhpSquad\DirectoryCreator\Repositories;


use Illuminate\Database\Capsule\Manager;
use PhpSquad\DirectoryCreator\Models\Directory;

class DirectoryRepository
{
    const NAV_DIRECTORY_TABLE = 'nav_directories';
    private Manager $database;

    public function __construct(Manager $database)
    {
        $this->database = $database;
    }

    public function create(string $accountId, ?string $parentId, string $type, string $name): Directory
    {
        $parentId = $parentId ? $parentId : 'base_nav_element';

        $dir = new Directory();
        $dir->account_id = $accountId;
        $dir->parent_id = $parentId;
        $dir->type = $type;
        $dir->name =$name;
        $dir->save();

        return $dir;
    }

    public function getDirectories(string $accountId)
    {
        return Directory::where('account_id', '=', $accountId)
            ->where('parent_id', '=', 'base_nav_element')
            ->with('lineage')
            ->get();
    }
}