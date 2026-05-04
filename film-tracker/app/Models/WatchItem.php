<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchItem extends Model
{
    protected $fillable = [
        'user_id',
        'imdb_id',
        'title',
        'poster',
        'year',
        'status',
    ];
}
