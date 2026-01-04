<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status',
        'starts_on',
        'ends_on',
        'description',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}
