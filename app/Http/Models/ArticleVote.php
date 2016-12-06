<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleVote extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'delete_at'
    ];

    public function user(){
        return $this->belongsTo('\App\Http\Models\User');
    }

    public function article(){
        return $this->belongsTo('\App\Http\Models\Article')->with('topic');
    }

}
