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

        $parentId = $baseDir->id;

        $projectDir = $directoryCreator->create($accountId, $parentId, 'project', 'Rocket Project');

        $parentId = $projectDir->id;

        $projectDir = $directoryCreator->create($accountId, $parentId, 'folder', 'Rocket Folder');

        $directories = $directoryCreator->getDirectories($accountId);

        $this->assertJson(
            json_encode([
                [
                    "id" => "80a5dcaf-a344-45d4-8fc8-01d4a609480c",
                    "account_id" => "my-uuid",
                    "type" => "team",
                    "name" => "Rocket Team",
                    "parent_id" => "base_nav_element",
                    "lineage" => [
                        [
                            "id" => "01bafb75-11c3-4c1e-921c-45ad13218b6f",
                            "account_id" => "my-uuid",
                            "type" => "project",
                            "name" => "Rocket Project",
                            "parent_id" => "80a5dcaf-a344-45d4-8fc8-01d4a609480c",
                            "children" => [
                                [
                                    "id" => "13b9f41a-8182-4c19-8a85-b7c1d80862dc",
                                    "account_id" => "my-uuid",
                                    "type" => "folder",
                                    "name" => "Rocket Folder",
                                    "parent_id" => "01bafb75-11c3-4c1e-921c-45ad13218b6f",
                                ]
                            ]
                        ]
                    ]
                ]
            ]), $directories);
    }
}