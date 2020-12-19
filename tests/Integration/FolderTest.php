<?php

namespace PhpSquad\NavDirectory\Tests\Integration;

use Illuminate\Database\Capsule\Manager;
use PhpSquad\NavDirectory\Repositories\DirectoryRepository;
use PhpSquad\NavDirectory\Services\Folder;
use PhpSquad\NavDirectory\Tests\TestDatabase;
use PHPUnit\Framework\TestCase;


class FolderTest extends TestCase
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
        $directoryCreator = new Folder($directoryRepository);
        $userId = 'user-one-uuid';
        $icon = 'mdi-folder';

        $directoryCreator->create('my-uuid', $userId, 'parent-uuid-1', 'team', 'Rocket Team', $icon);

        $record = $this->database->table(self::NAV_DIRECTORY_TABLE)
            ->select('name')
            ->first();

        $this->assertEquals('Rocket Team', $record->name);
    }

    public function testGetReturnsNestedDirectoriesAndDemonstratesInfiniteRecursion()
    {
        $directoryRepository = new DirectoryRepository();
        $navDirectory = new Folder($directoryRepository);

        $accountId = 'my-uuid';
        $teamDirName = 'Rocket Team';
        $teamDirType = 'team';
        $rootId = "base_nav_element";
        $userId = 'user-one-uuid';
        $icon = 'mdi-folder';

        $teamDir = $navDirectory->create(
            $accountId,
            $userId,
            null,
            $teamDirType,
            $teamDirName,
            $icon
        );

        $teamId = $teamDir->id;
        $createdAt = $teamDir->created_at;

        $projectDirName = 'Rocket Project';
        $projectDirType = 'project';
        $projectDir = $navDirectory->create(
            $accountId,
            $userId,
            $teamId,
            $projectDirType,
            $projectDirName,
            $icon
        );

        $projectId = $projectDir->id;

        $rocketFolderDirName = 'Rocket Folder';
        $rocketFolderDirType = 'folder';
        $folderDir = $navDirectory->create(
            $accountId,
            $userId,
            $projectId,
            $rocketFolderDirType,
            $rocketFolderDirName,
            $icon
        );
        $rocketFolderId = $folderDir->id;

        $shipFolderDirName = 'Ship Folder';
        $shipFolderDirType = 'folder';
        $folderDir = $navDirectory->create(
            $accountId,
            $userId,
            $projectId,
            $shipFolderDirType,
            $shipFolderDirName,
            $icon
        );
        $shipFolderId = $folderDir->id;

        $subShipFolderDirName = 'Sub Ship Folder';
        $subShipFolderDirType = 'folder';
        $folderDir = $navDirectory->create(
            $accountId,
            $userId,
            $shipFolderId,
            $subShipFolderDirType,
            $subShipFolderDirName,
            $icon
        );
        $subShipFolderId = $folderDir->id;

        $subSubShipFolderDirName = 'Sub-Sub Ship Folder';
        $subSubShipFolderDirType = 'folder';
        $folderDir = $navDirectory->create(
            $accountId,
            $userId,
            $subShipFolderId,
            $subSubShipFolderDirType,
            $subSubShipFolderDirName,
            $icon
        );
        $subSubShipFolderId = $folderDir->id;

        $directories = $navDirectory->list($accountId, $rootId);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => $teamId,
                    "account_id" => $accountId,
                    "user_id" => $userId,
                    "type" => $teamDirType,
                    "name" => $teamDirName,
                    "parent_id" => $rootId,
                    "icon" => $icon,
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                    "children" => [
                        [
                            "id" => $projectId,
                            "account_id" => $accountId,
                            "user_id" => $userId,
                            "type" => $projectDirType,
                            "name" => $projectDirName,
                            "parent_id" => $teamId,
                            "icon" => $icon,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => [
                                [
                                    "id" => $rocketFolderId,
                                    "account_id" => $accountId,
                                    "user_id" => $userId,
                                    "type" => $rocketFolderDirType,
                                    "name" => $rocketFolderDirName,
                                    "parent_id" => $projectId,
                                    "icon" => $icon,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                    "children" => []
                                ]
                                ,
                                [
                                    "id" => $shipFolderId,
                                    "account_id" => $accountId,
                                    "user_id" => $userId,
                                    "type" => $shipFolderDirType,
                                    "name" => $shipFolderDirName,
                                    "parent_id" => $projectId,
                                    "icon" => $icon,
                                    "created_at" => $createdAt,
                                    "updated_at" => $createdAt,
                                    "children" => [
                                        [
                                            "id" => $subShipFolderId,
                                            "account_id" => $accountId,
                                            "user_id" => $userId,
                                            "type" => $subShipFolderDirType,
                                            "name" => $subShipFolderDirName,
                                            "parent_id" => $shipFolderId,
                                            "icon" => $icon,
                                            "created_at" => $createdAt,
                                            "updated_at" => $createdAt,
                                            "children" => [
                                                [
                                                    "id" => $subSubShipFolderId,
                                                    "account_id" => $accountId,
                                                    "user_id" => $userId,
                                                    "type" => $subSubShipFolderDirType,
                                                    "name" => $subSubShipFolderDirName,
                                                    "parent_id" => $subShipFolderId,
                                                    "icon" => $icon,
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
        $navDirectory = new Folder($directoryRepository);

        $accountId = 'my-uuid';
        $teamDirName = 'Rocket Team';
        $teamDirType = 'team';
        $rootId = "base_nav_element";
        $userId = 'user-one-uuid';
        $icon = 'mdi-folder';


        $teamDir = $navDirectory->create($accountId, $userId, $rootId, $teamDirType, $teamDirName, $icon);
        $teamId = $teamDir->id;
        $createdAt = $teamDir->created_at;


        $projectDirName = 'Rocket Project';
        $projectDirType = 'project';
        $projectDir = $navDirectory->create($accountId, $userId, $teamId, $projectDirType, $projectDirName, $icon);
        $projectId = $projectDir->id;

        $folderDirName = 'Rocket Folder';
        $folderDirType = 'folder';
        $folderDir = $navDirectory->create($accountId, $userId, $projectId, $folderDirType, $folderDirName, $icon);
        $folderId = $folderDir->id;

        $projectDirName2 = 'Ship Project';
        $projectDirType2 = 'project';
        $projectDir2 = $navDirectory->create($accountId, $userId, $teamId, $projectDirType2, $projectDirName2, $icon);
        $projectId2 = $projectDir2->id;

        $newFolderDirName = 'Updated Folder Name';

        $navDirectory->update(
            $folderId,
            $accountId,
            $userId,
            $projectId2,
            $folderDirType,
            $newFolderDirName,
            $icon
        );

        $directories = $navDirectory->list($accountId, $rootId);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => $teamId,
                    "account_id" => $accountId,
                    "user_id" => $userId,
                    "type" => $teamDirType,
                    "name" => $teamDirName,
                    "parent_id" => $rootId,
                    "icon" => $icon,
                    "created_at" => $createdAt,
                    "updated_at" => $createdAt,
                    "children" => [
                        [
                            "id" => $projectId,
                            "account_id" => $accountId,
                            "user_id" => $userId,
                            "type" => $projectDirType,
                            "name" => $projectDirName,
                            "parent_id" => $teamId,
                            "icon" => $icon,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => []
                        ],
                        [
                            "id" => $projectId2,
                            "account_id" => $accountId,
                            "user_id" => $userId,
                            "type" => $projectDirType2,
                            "name" => $projectDirName2,
                            "parent_id" => $teamId,
                            "icon" => $icon,
                            "created_at" => $createdAt,
                            "updated_at" => $createdAt,
                            "children" => [
                                [
                                    "id" => $folderId,
                                    "account_id" => $accountId,
                                    "user_id" => $userId,
                                    "type" => $folderDirType,
                                    "name" => $newFolderDirName,
                                    "parent_id" => $projectId2,
                                    "icon" => $icon,
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