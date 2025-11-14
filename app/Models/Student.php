<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_english',
        'name_kana',
        'nationality',
        'gender',
        'age',
        'email',
        'jlpt_level',
        'school_id',
        'student_number',
        'home_country_education',
        'referrer',
        'oc_attendance',
        'oc_reservation_date',
        'online',
        'enrollment_year',
        'status',
        'applied',
        'participated',
    ];

    protected $casts = [
        'oc_reservation_date' => 'date',
        'online' => 'boolean',
        'oc_attendance' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}

