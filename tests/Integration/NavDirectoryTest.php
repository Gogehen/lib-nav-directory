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

    public function testGetReturnsNestedDirectoriesAndDemonstratesInfiniteRecursion()
    {
        $directoryRepository = new DirectoryRepository();
        $navDirectory = new NavDirectory($directoryRepository);

        $accountId = 'my-uuid';
        $teamDirName = 'Rocket Team';
        $teamDirType = 'team';
        $baseElementId = "base_nav_element";

        $teamDir = $navDirectory->create($accountId, null, $teamDirType, $teamDirName);
        $teamId = $teamDir->id;
        $createdAt = $teamDir->created_at;


        $projectDirName = 'Rocket Project';
        $projectDirType = 'project';
        $projectDir = $navDirectory->create($accountId, $teamId, $projectDirType, $projectDirName);
        $projectId = $projectDir->id;

        $rocketFolderDirName = 'Rocket Folder';
        $rocketFolderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $projectId, $rocketFolderDirType, $rocketFolderDirName);
        $rocketFolderId = $folderDir->id;

        $shipFolderDirName = 'Ship Folder';
        $shipFolderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $projectId, $shipFolderDirType, $shipFolderDirName);
        $shipFolderId = $folderDir->id;

        $subShipFolderDirName = 'Sub Ship Folder';
        $subShipFolderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $shipFolderId, $subShipFolderDirType, $subShipFolderDirName);
        $subShipFolderId = $folderDir->id;

        $subSubShipFolderDirName = 'Sub-Sub Ship Folder';
        $subSubShipFolderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $subShipFolderId, $subSubShipFolderDirType, $subSubShipFolderDirName);
        $subSubShipFolderId = $folderDir->id;

        $directories = $navDirectory->getDirectories($accountId);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => $teamId,
                    "account_id" => $accountId,
                    "type" => $teamDirType,
                    "name" => $teamDirName,
                    "parent_id" => $baseElementId,
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                    "children" => [
                        [
                            "id" => $projectId,
                            "account_id" => $accountId,
                            "type" => $projectDirType,
                            "name" => $projectDirName,
                            "parent_id" => $teamId,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => [
                                [
                                    "id" => $rocketFolderId,
                                    "account_id" => $accountId,
                                    "type" => $rocketFolderDirType,
                                    "name" => $rocketFolderDirName,
                                    "parent_id" => $projectId,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                    "children" => []
                                ]
                                ,
                                [
                                    "id" => $shipFolderId,
                                    "account_id" => $accountId,
                                    "type" => $shipFolderDirType,
                                    "name" => $shipFolderDirName,
                                    "parent_id" => $projectId,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                    "children" => [
                                        [
                                            "id" => $subShipFolderId,
                                            "account_id" => $accountId,
                                            "type" => $subShipFolderDirType,
                                            "name" => $subShipFolderDirName,
                                            "parent_id" => $shipFolderId,
                                            "created_at" => $createdAt,
                                            "updated_at" => $createdAt,
                                            "children" => [
                                                [
                                                    "id" => $subSubShipFolderId,
                                                    "account_id" => $accountId,
                                                    "type" => $subSubShipFolderDirType,
                                                    "name" => $subSubShipFolderDirName,
                                                    "parent_id" => $subShipFolderId,
                                                    "created_at" => $createdAt,
                                                    "updated_at" => $createdAt,
                                                    "children" => []
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]), $directories);
    }

    public function testUpdateWithMoveToDifferentParent()
    {
        $directoryRepository = new DirectoryRepository();
        $navDirectory = new NavDirectory($directoryRepository);

        $accountId = 'my-uuid';
        $teamDirName = 'Rocket Team';
        $teamDirType = 'team';
        $baseElementId = "base_nav_element";

        $teamDir = $navDirectory->create($accountId, null, $teamDirType, $teamDirName);
        $teamId = $teamDir->id;
        $createdAt = $teamDir->created_at;


        $projectDirName = 'Rocket Project';
        $projectDirType = 'project';
        $projectDir = $navDirectory->create($accountId, $teamId, $projectDirType, $projectDirName);
        $projectId = $projectDir->id;

        $projectDirName2 = 'Ship Project';
        $projectDirType2 = 'project';
        $projectDir2 = $navDirectory->create($accountId, $teamId, $projectDirType2, $projectDirName2);
        $projectId2 = $projectDir2->id;

        $folderDirName = 'Rocket Folder';
        $folderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $projectId, $folderDirType, $folderDirName);
        $folderId = $folderDir->id;

        $newFolderDirName = 'Updated Folder Name';

        $data = (object) [
            'accountId' => $accountId,
            'id' => $folderId,
            'parentId' => $projectId2,
            'type' => $folderDirType,
            'name' => $newFolderDirName
        ];

        $navDirectory->update($data);


        $directories = $navDirectory->getDirectories($accountId);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => $teamId,
                    "account_id" => $accountId,
                    "type" => $teamDirType,
                    "name" => $teamDirName,
                    "parent_id" => $baseElementId,
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                    "children" => [
                        [
                            "id" => $projectId,
                            "account_id" => $accountId,
                            "type" => $projectDirType,
                            "name" => $projectDirName,
                            "parent_id" => $teamId,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => []
                        ],
                        [
                            "id" => $projectId2,
                            "account_id" => $accountId,
                            "type" => $projectDirType2,
                            "name" => $projectDirName2,
                            "parent_id" => $teamId,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => [
                                [
                                    "id" => $folderId,
                                    "account_id" => $accountId,
                                    "type" => $folderDirType,
                                    "name" => $newFolderDirName,
                                    "parent_id" => $projectId2,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                    "children" => [],
                                ]
                            ]
                        ]
                    ]
                ]
            ]), $directories);
    }
}