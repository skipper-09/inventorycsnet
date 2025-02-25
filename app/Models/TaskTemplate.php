<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaskTemplate extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "name",
        "slug",
        "description",
    ];

    public function tasks()
    {
        return $this->hasMany(Template_task::class,'task_template_id','id');
    }

    public function taskAssign()
    {
        return $this->hasOne(TaskAssign::class);
    }


    public function setSlugAttribute($value)
    {
        if (empty($this->attributes['slug']) || $this->isDirty('name')) {
            $this->attributes['slug'] = Str::slug($this->name);
        }
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
