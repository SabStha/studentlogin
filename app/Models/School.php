<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'postal_code',
        'address',
        'contact_person',
        'position',
        'phone',
        'fax',
        'email',
        'website',
        'memo',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}

