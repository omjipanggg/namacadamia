<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:191',
            'phone' => 'required|string|min:3|max:191|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'is_active' => 'nullable|boolean',
            'member_points' => 'nullable|integer|min:0',
        ]);

        $customer = Customer::create($validated);
        return CustomerResource::make($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:191',
            'phone' => 'required|string|min:3|max:191|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'is_active' => 'nullable|boolean',
            'member_points' => 'nullable|integer|min:0',
        ]);

        $customer->update($validated);
        return CustomerResource::make($customer);
    } 

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
    }
}
