<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSnapshot extends Model
{
    protected $fillable = ['period','income','expenses','commissions','profit'];
    protected $casts = ['period'=>'date','income'=>'decimal:2','expenses'=>'decimal:2','commissions'=>'decimal:2','profit'=>'decimal:2'];
}
