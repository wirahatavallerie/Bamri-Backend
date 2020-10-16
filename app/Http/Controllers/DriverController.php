<?php

namespace App\Http\Controllers;

use App\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'age' => 'integer|required|min:18',
            'id_number' => 'required|string|min:16|max:16',
        ]);

        if(!$validator->fails()){
            $driver = new Driver();
            $driver->name = $request->name;
            $driver->age = $request->age;
            $driver->id_number = $request->id_number;
            if($driver->save()){
                return response()->json([
                    'message' => 'create driver success'
                ], 200);
            }
        }

        return response()->json([
            'message' => 'invalid field'
        ], 422);
    }

    public function update(Request $request, $driver_id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'age' => 'integer|required|min:18',
            'id_number' => 'required|string|min:16|max:16',
        ]);

        if(!$validator->fails()){
            $driver = Driver::where('id', $driver_id)->first();
            $driver->name = $request->name;
            $driver->age = $request->age;
            $driver->id_number = $request->id_number;
            if($driver->save()){
                return response()->json([
                    'message' => 'update driver success'
                ], 200);
            }
        }

        return response()->json([
            'message' => 'invalid field'
        ], 422);
    }

    public function delete(Request $request, $driver_id){
        $driver = Driver::where('id', $driver_id)->delete();
        if($driver){
            return response()->json([
                'message' => 'delete driver success'
            ], 200);
        }

        return response()->json([
            'message' => 'unauthorized user'
        ], 403);
    }

    public function get(Request $request){
        $driver = Driver::select('id', 'name', 'age', 'id_number')->get();
        return response()->json([
            'drivers' => $driver
        ], 200);
    }
}
