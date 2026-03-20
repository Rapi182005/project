<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    // New: Show Edit Form
    public function edit($id)
    {
        if (!session('user_logged_in')) return redirect()->route('login');

        $order = DB::table('orders')->where('id', $id)->first();
        
        // Ensure user can only edit their own pending orders
        if (!$order || $order->user_id != session('user_id') || $order->status != 'Pending') {
            return redirect()->route('user.orders')->with('error', 'Order cannot be edited.');
        }

        return view('edit_order', compact('order'));
    }

    // New: Update Order Note
    public function update(Request $request, $id)
    {
        DB::table('orders')->where('id', $id)->update([
            'message' => $request->message,
            'updated_at' => now()
        ]);

        return redirect()->route('user.orders')->with('success', 'Order updated successfully!');
    }

    // New: Delete/Cancel Order
    public function delete($id)
    {
        DB::table('orders')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Order cancelled successfully.');
    }

    public function redirectTrack(Request $request)
    {
        // Security Check
        if (!session('admin_logged_in')) {
            return redirect()->route('login');
        }

        $number = $request->input('tracking_number');
        $courier = $request->input('courier');

        // Save to database
        DB::table('shipments')->updateOrInsert(
            ['tracking_number' => $number],
            [
                'courier' => $courier, 
                'status' => 'Pending',
                'created_at' => now(), 
                'updated_at' => now()
            ]
        );

        if ($courier == 'JT') {
            return redirect()->away("https://www.jtexpress.ph/index/query/gzquery.html?bills=" . $number);
        } elseif ($courier == 'LBC') {
            return redirect()->away("https://www.lbcexpress.com/track/?tracking_number=" . $number);
        }

        return redirect()->back()->with('error', 'Please select a courier.');
    }
}