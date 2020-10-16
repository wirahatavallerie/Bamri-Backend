<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'contact_name' => 'required|string',
            'contact_phone' => 'string|required|digits:12',
            'start_rent_date' => 'required|date|after:today',
            'total_rent_days' => 'required|integer|min:1',
        ]);

        if(!$validator->fails()){
            $date = new DateTime($request->start_rent_date);
            $total_rent = $request->total_rent_days -1;
            $end_date = $date->add(new DateInterval("P".$request->total_rent_days."D"));

            $end_rent_date = date_format($end_date, 'Y-m-d');
            $start_rent_date = $request->start_rent_date;

            $order_check = Order::where('bus_id', $request->bus_id)
                                ->where('driver_id', $request->driver_id)
                                ->where(function($q) use ($start_rent_date, $end_rent_date){
                                    $q
                                    ->whereBetween(DB::Raw("'$start_rent_date'"), ['start_rent_date', 
                                    DB::Raw('date_add(start_rent_date, INTERVAL total_rent_days DAY)')]);
                                    // ->whereBetween(DB::Raw("'$end_rent_date'"), ['start_rent_date', 
                                    // DB::Raw('date_add(start_rent_date, INTERVAL total_rent_days DAY)')]);
                                })
                                ->get();

            if(!$order_check ){
                $order = new Order();
                $order->bus_id = $request->bus_id;
                $order->driver_id = $request->driver_id;
                $order->contact_name = $request->contact_name;
                $order->contact_phone = $request->contact_phone;
                $order->start_rent_date = $request->start_rent_date;
                $order->total_rent_days = $request->total_rent_days;
                if($order->save()){
                    $order = Order::join('bus', 'bus.id', 'orders.bus_id')
                                    ->join('drivers', 'drivers.id', 'orders.driver_id')
                                    ->select('orders.id', 'orders.contact_name', 'orders.contact_phone',
                                            'orders.start_rent_date', 'orders.total_rent_days',
                                            'bus.id as bus_id', 'drivers.id as driver_id', 
                                            'bus.plate_number as bus', 'drivers.name as driver')
                                    ->first();
                    return response()->json([
                        'message' => 'create order success'
                    ], 200);
                }
            }

            return response()->json([
                'message' => 'Conflicting while order unavailble bus or driver'
            ], 422);
        }

        return response()->json([
            'message' => 'invalid field'
        ], 422);
    }

    public function delete(Request $request, $order_id){
        Order::where('id', $order_id)->delete();
        return response()->json([
            'message' => 'delete order success'
        ], 200);
    }

    public function get(Request $request){
        $order = Order::join('bus', 'bus.id', 'orders.bus_id')
                        ->join('drivers', 'drivers.id', 'orders.driver_id')
                        ->select('orders.id', 'orders.contact_name', 'orders.contact_phone',
                                'orders.start_rent_date', 'orders.total_rent_days',
                                'bus.id as bus_id', 'drivers.id as driver_id', 
                                'bus.plate_number as bus', 'drivers.name as driver')->get();
        return response()->json([
            'orders' => $order
        ], 200);
    }
}
