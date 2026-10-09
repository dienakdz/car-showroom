<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Setting extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
        ];
    }
}
