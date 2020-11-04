<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $table = 'items';

    protected $fillable = ['name', 'description', 'price', 'is_published'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $categories;

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
