<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'content', 'user_id', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public function user() { return $this->belongsTo(User::class); }
}