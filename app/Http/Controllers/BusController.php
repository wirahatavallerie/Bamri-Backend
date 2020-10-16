<?php

namespace App\Http\Controllers;

use App\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BusController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'plate_number' => 'required|string|unique:bus',
            'brand' => Rule::in(['mercedes', 'fuso', 'scania']),
            'seat' => 'required|numeric|min:1',
            'price_per_day' => 'required|numeric|min:100000'
        ]);

        if(!$validator->fails()){
            $bus = new Bus;
            $bus->plate_number = $request->plate_number;
            $bus->brand = $request->brand;
            $bus->seat = $request->seat;
            $bus->price_per_day = $request->price_per_day;
            if($bus->save()){
                return response()->json([
                    'message' => 'create bus success'
                ], 200);
            }
        }

        return response()->json([
            'message' => 'invalid field'
        ], 422);
    }

    public function update(Request $request, $bus_id){
        $validator = Validator::make($request->all(), [
            'plate_number' => 'string|required',
            'brand' => Rule::in(['mercedes', 'fuso', 'scania']),
            'seat' => 'numeric|min:1|required',
            'price_per_day' => 'numeric|min:100000|required'
        ]);

        if(!$validator->fails()){
            $bus = Bus::where('id', $bus_id)->first();
            $bus->plate_number = $request->plate_number;
            $bus->brand = $request->brand;
            $bus->seat = $request->seat;
            $bus->price_per_day = $request->price_per_day;
            if($bus->save()){
                return response()->json([
                    'message' => 'update bus success'
                ], 200);
            }
        }

        return response()->json([
            'message' => 'invalid field'
        ], 422);
    }

    public function delete(Request $request, $bus_id){
        $bus = Bus::where('id', $bus_id)->delete();
        return response()->json([
            'message' => 'delete bus success'
        ], 200);
    }

    public function get(Request $request){
        $bus = Bus::select('id', 'plate_number', 'brand', 'seat', 'price_per_day')->get();
        return response()->json([
            'buses' => $bus
        ], 200);
    }
    
}
