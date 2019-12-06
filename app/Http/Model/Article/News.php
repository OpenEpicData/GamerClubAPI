<?php

namespace App\Http\Model\Article;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = [];

    public function tag()
    {
        return $this->belongsTo('App\Http\Model\Article\Tag');
    }

    public function ref()
    {
        return $this->belongsTo('App\Http\Model\Article\Ref');
    }
}
