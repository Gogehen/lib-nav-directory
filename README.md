# PhpSquad/ProjectManager *Nav-Directory*

>Create and Get Navigation Directory. 

### Usage

```shell script
composer install phpsquad/nav-directory
```

#### Example Usage

```php
class NavDirectoryController extends Controller
{
    private $navigationDirectory;

    public function __construct(NavigationDirectory $navigationDirectory)
    {
        $this->navigationDirectory = $navigationDirectory;
    }

    public function index(Request $request)
    {
        $accountId = 'example-account-uuid';

        $navDir = $this->navigationDirectory->getListByAccountId($accountId);

        return response()->json($navDir, 200);
    }

    public function store(Request $request)
    {
        $accountId = 'example-account-uuid';
        $parentId = $request->parentId;
        $type = $request->type;
        $name = $request->name;

        $navDir = $this->navigationDirectory->create($accountId, $parentId, $type, $name);

        return response()->json($navDir, 200);
    }
}
```