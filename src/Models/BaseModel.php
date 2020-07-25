<?php


namespace PhpSquad\NavDirectory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

if ($_ENV["APP_ENV"] == 'testing'){
    require 'config/database.php';
}


class BaseModel extends Model
{
    protected $casts = [
        'id' => 'string',
        'accounts_id' => 'string'
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if($this->id == null){
            $this->id = Str::uuid();
        }
    }
}