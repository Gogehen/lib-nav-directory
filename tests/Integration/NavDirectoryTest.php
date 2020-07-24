<?php

namespace PhpSquad\NavDirectory\Tests\Integration;

use Illuminate\Database\Capsule\Manager;
use PhpSquad\NavDirectory\Repositories\DirectoryRepository;
use PhpSquad\NavDirectory\Services\NavDirectory;
use PhpSquad\NavDirectory\Tests\TestDatabase;
use PHPUnit\Framework\TestCase;


class NavDirectoryTest extends TestCase
{
    use TestBase;

    const NAV_DIRECTORY_TABLE = 'directories';
    private Manager $database;

    public function setUp(): void
    {
        $this->database = TestDatabase::connect();
        $this->createTables();
        $this->insertTestData();
    }

    public function testCreateDirectory()
    {
        $directoryRepository = new DirectoryRepository();
        $directoryCreator = new NavDirectory($directoryRepository);

        $directoryCreator->create('my-uuid', 'parent-uuid-1', 'team', 'Rocket Team');

        $record = $this->database->table(self::NAV_DIRECTORY_TABLE)
            ->select('name')
            ->first();

        $this->assertEquals('Rocket Team', $record->name);
    }

    public function testGetNestedDirectories()
    {
        $directoryRepository = new DirectoryRepository();
        $directoryCreator = new NavDirectory($directoryRepository);

        $accountId = 'my-uuid';

        $baseDir = $directoryCreator->create($accountId, null, 'team', 'Rocket Team');

        $teamId = $baseDir->id;
        $createdAt = $baseDir->created_at;

        $projectDir = $directoryCreator->create($accountId, $teamId, 'project', 'Rocket Project');

        $projectId = $projectDir->id;

        $folder = $directoryCreator->create($accountId, $projectId, 'folder', 'Rocket Folder');
        $folderId = $folder->id;

        $directories = $directoryCreator->getDirectories($accountId);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => $teamId,
                    "account_id" => "my-uuid",
                    "type" => "team",
                    "name" => "Rocket Team",
                    "parent_id" => "base_nav_element",
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                    "projects" => [
                        [
                            "id" => $projectId,
                            "account_id" => "my-uuid",
                            "type" => "project",
                            "name" => "Rocket Project",
                            "parent_id" => $teamId,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "folders" => [
                                [
                                    "id" => $folderId,
                                    "account_id" => "my-uuid",
                                    "type" => "folder",
                                    "name" => "Rocket Folder",
                                    "parent_id" => $projectId,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                ]
                            ]
                        ]
                    ]
                ]
            ]), $directories);
    }
}