<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OvertimeFormula extends Model
{
    protected $fillable = [
        'name',
        'formula',
    ];
}
