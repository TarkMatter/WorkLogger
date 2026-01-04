<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
