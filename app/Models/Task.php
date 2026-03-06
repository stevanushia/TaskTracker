<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 👈 Import SoftDeletes

class Task extends Model
{
    use HasFactory, SoftDeletes; // 👈 Enable soft deletes here

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'project_id',
        'category_id',
        'created_by',
        'deleted_by'
    ];

    public function project() { return $this->belongsTo(Project::class); }
    public function category() { return $this->belongsTo(Category::class); }
}
