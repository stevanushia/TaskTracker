<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // 1. Allow these fields to be saved
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by'
    ];

    // 2. Define the relationship to the User who created it
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 3. Define the relationship to Tasks (which we used in the controller)
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
