<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidaysController extends Controller
{
    public function index($year = ''){
        if ($year == '') {
            $year = Carbon::now()->year;
        }
        
        $holidays = DB::table('holiday')->where('year',$year)->get();
        return view('admin.holidays.index',compact('holidays','year'));
    }
    public function holidaysYearSearch($year){
        $holidays = DB::table('holiday')->where('year',$year)->get();
        // return redirect()->route('holidays')->with('year', $year);
        return view('admin.holidays.index',compact('holidays','year'));
    }
    public function fetchNationHolidays(Request $request){
        $apiKey = 'K0h9Nnd2gCbC2RA8KPDoOjgU3tGvGxE0';
        $year = $request->year; // Get current year
        $country = 'PH'; // Philippines country code
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Send a GET request to the API
            $response = $client->get("https://calendarific.com/api/v2/holidays", [
                'query' => [
                    'api_key' => $apiKey,
                    'country' => $country,
                    'year' => $year,
                    'type' => 'national',
                ],
            ]);

            // Get the response body as a string
            $body = $response->getBody()->getContents();

            // Parse the JSON response
            $data = json_decode($body, true);

            $holidays = $data['response']['holidays'];
            // Format the holiday dates using Carbon
            $formattedHolidays = [];
            foreach ($holidays as $holiday) {
                $date = Carbon::parse($holiday['date']['iso']);
                $formattedHolidays[] = [
                    'name' => $holiday['name'],
                    'date' => $date->toDateString(),
                ];
                $check = DB::table('holiday')->where('year',$request->year)->where('holiday_name',$holiday['name'])->first();
                if ($check === null) {
                    // The holiday does not exist in the database
                    DB::table('holiday')->insert([
                        'holiday_name' => $holiday['name'],
                        'year' => $request->year,
                        'date_from'=> $date->toDateString(),
                        'date_to'=>$date->toDateString(),
                        'created_at'=>Carbon::now(),
                        'created_at2'=>Carbon::now()->format('Y-m-d'),
                    ]);
                }
                
            }
            // return response()->json($formattedHolidays);
            return redirect()->route('holidays')->with('message', 'Holidays Processed Successfully!');
        } catch (RequestException $e) {
            // Handle request exceptions (e.g., network error, invalid URL, etc.)
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request){
        if(($request->year !== Carbon::parse($request->date_from)->format('Y')) || ($request->year !== Carbon::parse($request->date_to)->format('Y'))){
            return redirect()->route('holidays')->with('message', 'error! trying to update mismatched holiday year');
        }
        $update = DB::table('holiday')
        ->where('id',$request->id)
        ->update([
            'holiday_name'=>$request->name,
            'date_from'=>$request->date_from,
            'date_to'=>$request->date_to,
        ]);

        if($update){
            return redirect()->route('holidays')->with('message', 'Date Updated Successfully!');
        }

        return redirect()->route('holidays')->with('message', 'Something Went Wrong!');

    }
    public function store(Request $request){
        $check = DB::table('holiday')
        ->where('holiday_name',$request->holiday_name)
        ->where('year',$request->year)
        ->first();

        if ($check === null) {
            // The holiday does not exist in the database
            DB::table('holiday')->insert([
                'holiday_name' => $request->holiday_name,
                'year' => $request->year,
                'date_from'=> $request->date_from,
                'date_to'=>$request->date_to,
                'created_at'=>Carbon::now(),
                'created_at2'=>Carbon::now()->format('Y-m-d'),
            ]);

            return redirect()->route('holidays')->with('message', 'Holiday Successfully Added!');
        }

        return redirect()->route('holidays')->with('message', 'Holiday Already Exist!');
    }
    public function delete(Request $request)
    {
        // The holiday does not exist in the database
        $delete = DB::table('holiday')->where('id',$request->id)->where('year',$request->year)->delete();
        if($delete){
            return redirect()->route('holidays')->with('message', 'Holiday Successfully Deleted!');
        }

        return redirect()->route('holidays')->with('message', 'Holiday Something went wrong!');
    }
}
