<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'sid';
    public $timestamps = false;
    
    protected $fillable = [
        'fname',
        'lname',
        'birthplace',
        'birthDate'
    ];
}