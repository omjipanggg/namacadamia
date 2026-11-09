<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;

use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = Order::create($request->all);

        $superusers = User::role('admin')->get()->each(fn ($user) => $user->notify(new NewOrderNotification($order)));
        
        return $this->sendResponse(true, 'New order has been placed', $order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
