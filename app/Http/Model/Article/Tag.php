<?php

namespace App\Http\Model\Article;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected array $guarded = [];

    public function news()
    {
        return $this->hasMany('App\Http\Model\Article\News');
    }
}
