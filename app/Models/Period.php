<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Period extends Model
{
    use HasFactory;
    use DateFormattor;

    protected $fillable = ['start', 'end', 'object', 'description', 'school_year_id', 'target', 'semestre', 'closed', 'authorized', 'blocked'];



    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    /**
     * To get Duration of an event or of a period model defined
        @return string
     */
    public function getDuration()
    {
        $start_string = $this->__getDateAsString($this->start, false);
        $end_string = $this->__getDateAsString($this->end, false);
        $weeks = Carbon::parse($this->end)->floatDiffInRealWeeks($this->start);
        $days = floor(($weeks - floor($weeks)) * 7);
        $duration = floor($weeks) . ' Semaines ' . $days . ' Jours';

        return $duration;
    }
}
