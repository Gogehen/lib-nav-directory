<?php

namespace PhpSquad\NavDirectory\Models;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class Directories extends DataTransferObjectCollection
{
    public static function create(Directory ...$directories)
    {
        return new self($directories);
    }
}