<?php

namespace PhpSquad\NavDirectory\Services;

use PhpSquad\NavDirectory\Models\Directory;
use PhpSquad\NavDirectory\Repositories\DirectoryRepository;

class Folder
{
    private DirectoryRepository $directoryRepository;

    public function __construct(DirectoryRepository $directoryRepository)
    {
        $this->directoryRepository = $directoryRepository;
    }

    public function create(
        string $accountId,
        string $userId,
        ?string $parentId,
        string $type,
        string $name,
        string $icon
    ): Directory {
        return $this->directoryRepository->create($accountId, $userId, $parentId, $type, $name, $icon);
    }

    public function list(string $accountId, string $rootId)
    {
        return $this->directoryRepository->list($accountId, $rootId);
    }

    public function update(
        string $id,
        string $accountId,
        string $userId,
        ?string $parentId,
        string $type,
        string $name,
        string $icon
    ) {
        return $this->directoryRepository->update($id, $accountId, $userId, $parentId, $type, $name, $icon);
    }

}