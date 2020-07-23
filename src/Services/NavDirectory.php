<?php

namespace PhpSquad\NavDirectory\Services;


use PhpSquad\NavDirectory\Models\Directory;
use PhpSquad\NavDirectory\Repositories\DirectoryRepository;

class NavDirectory
{
    private DirectoryRepository $directoryRepository;

    public function __construct(DirectoryRepository $directoryRepository)
    {
        $this->directoryRepository = $directoryRepository;
    }

    public function create($accountId, $parentId, $type, $name): Directory
    {
        return $this->directoryRepository->create($accountId, $parentId, $type, $name);
    }

    public function getDirectories(string $accountId)
    {
       return  $this->directoryRepository->getDirectories($accountId);
    }

}