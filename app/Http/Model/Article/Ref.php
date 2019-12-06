<?php

namespace App\Http\Model\Article;

use Illuminate\Database\Eloquent\Model;

class Ref extends Model
{
    protected $guarded = [];

    public function news()
    {
        return $this->hasMany('App\Http\Model\Article\News');
    }
}
