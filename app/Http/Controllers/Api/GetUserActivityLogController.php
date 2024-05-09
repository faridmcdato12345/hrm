<?php

namespace App\Http\Controllers\Api;

use datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class GetUserActivityLogController extends Controller
{
    public function getUserActivityLog(){
        $a = Activity::all();
        $b = $a->map(function($item,$key){
            $causer_fname = DB::table('employees')->where('id',$item['causer_id'])->value('firstname');
            $causer_lname = DB::table('employees')->where('id',$item['causer_id'])->value('lastname');
            return [
                'subject_id' => DB::table('employees')->where('id',$item['subject_id'])->value('firstname'),
                'description' => $item['description'],
                'causer_id' => $causer_fname.' '.$causer_lname,
                'created_at' => $item['created_at']->toDateTimeString(),
                'updated_at' => $item['updated_at']->toDateTimeString(),
            ];
        });
        return datatables($b)->make(true);
    }
}
