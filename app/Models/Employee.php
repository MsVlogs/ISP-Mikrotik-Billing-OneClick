<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id','employee_no','department','job_title','salary','joined_at','status'];
    protected $casts = ['salary'=>'decimal:2','joined_at'=>'date'];
    public function user() { return $this->belongsTo(User::class); }
}
