<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AnimationCategory extends Model
{

    protected $guarded = [];

    public function subcategories()
    {
        return $this->hasMany(AnimationCategory::class, 'parent_id');
    }
}
