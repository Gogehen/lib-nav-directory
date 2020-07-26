# PhpSquad/ProjectManager *Nav-Directory*

>Create and Get Navigation Directory. 

### Usage

```shell script
composer install phpsquad/nav-directory
```

#### Example Usage

create a directory
```php
$navDirectory->create($accountId, $userId, $rootId, $type, $name, $icon);
```
update a directory
```php
$navDirectory->update($accountId, $userId, $rootId, $type, $name, $icon);
```

get List of directories
```php
$navDirectory->list(string $accountId, string $rootId)
```