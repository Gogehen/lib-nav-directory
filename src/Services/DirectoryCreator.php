<?php

namespace PhpSquad\DirectoryCreator\Services;


use PhpSquad\DirectoryCreator\Models\Directory;
use PhpSquad\DirectoryCreator\Repositories\DirectoryRepository;

class DirectoryCreator
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