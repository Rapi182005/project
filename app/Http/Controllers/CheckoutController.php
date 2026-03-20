<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\GmailService; // Import your Gmail Service

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart');
        if (!$cart) {
            return redirect()->route('user.dashboard')->with('error', 'Your bag is empty!');
        }

        $isTopCustomer = (session('user_role') === 'Top Customer');
        $discount = $isTopCustomer ? 0.90 : 1.0;

        $totalValue = 0;
        foreach($cart as $details) {
            $totalValue += ($details['price'] * $details['quantity']) * $discount;
        }

        $secretKey = env('XENDIT_SECRET_KEY');
        $externalId = 'vape-' . time();
        
        $data = [
            'external_id'          => $externalId,
            'amount'               => (float)$totalValue,
            'description'          => 'Vape Store Purchase',
            'currency'             => 'PHP',
            'success_redirect_url' => route('user.orders'),
            'failure_redirect_url' => url('/user-dashboard'),
        ];

        $response = Http::withBasicAuth($secretKey, '')
            ->post('https://api.xendit.co/v2/invoices', $data);

        if ($response->successful()) {
            $result = $response->json();

            foreach($cart as $details) {
                DB::table('orders')->insert([
                    'user_id'        => session('user_id'), 
                    'customer_name'  => session('user_name'),
                    'product_name'   => $details['name'],
                    'amount'         => ($details['price'] * $details['quantity']) * $discount,
                    'external_id'    => $externalId, 
                    'message'        => $request->message ?? 'No instructions',
                    'payment_status' => 'Unpaid',
                    'status'         => 'Pending',
                    'created_at'     => now(),
                ]);
            }

            session()->forget('cart');
            return redirect($result['invoice_url']);
        } else {
            return redirect()->route('user.dashboard')->with('error', 'Xendit Error: ' . $response->body());
        }
    }

    public function checkPaymentStatus($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        $secretKey = env('XENDIT_SECRET_KEY');

        if (!$order || !$order->external_id || $order->payment_status === 'Paid') {
            return false;
        }

        // Use the query parameter method to avoid the API_VALIDATION_ERROR
        $response = Http::withBasicAuth($secretKey, '')
            ->get("https://api.xendit.co/v2/invoices?external_id={$order->external_id}");

        if ($response->successful()) {
            $invoices = $response->json();
            
            if (!empty($invoices) && isset($invoices[0])) {
                $invoice = $invoices[0];
                $xenditStatus = strtoupper($invoice['status']);

                if (in_array($xenditStatus, ['PAID', 'SETTLED', 'COMPLETED'])) {
                    
                    // 1. Update the database column to 'Paid'
                    DB::table('orders')->where('external_id', $order->external_id)->update([
                        'payment_status' => 'Paid',
                        'updated_at'     => now()
                    ]);

                    // 2. TRIGGER THE GMAIL RECEIPT IMMEDIATELY
                    $user = DB::table('users')->where('id', $order->user_id)->first();

                    if ($user && !empty($user->email)) {
                        try {
                            $gmail = new GmailService();
                            $subject = "Payment Received - Order #{$order->external_id}";
                            
                            $body = "
                                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                                    <h2 style='color: #4f46e5;'>Payment Successful!</h2>
                                    <p>Hi <strong>{$user->name}</strong>,</p>
                                    <p>We have received your payment for your order.</p>
                                    <hr>
                                    <p><strong>Order ID:</strong> {$order->external_id}</p>
                                    <p><strong>Product:</strong> {$order->product_name}</p>
                                    <p><strong>Total Amount:</strong> ₱" . number_format($order->amount, 2) . "</p>
                                    <hr>
                                    <p>Your order status is now being processed for shipment. Thank you for your purchase!</p>
                                </div>
                            ";

                            $gmail->sendOrderEmail($user->email, $subject, $body);
                            Log::info("Receipt email sent to {$user->email} for Order #{$order->external_id}");
                        } catch (\Exception $e) {
                            Log::error("Gmail Receipt Error: " . $e->getMessage());
                        }
                    }
                    return true;
                }
            }
        } else {
            Log::error("Xendit Check Failed: " . $response->body());
        }
        return false;
    }
}