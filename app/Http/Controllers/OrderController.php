<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->latest()->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'payment_method' => 'required|string',
            'total_price'    => 'required|numeric',
            'items'          => 'required|array',
            'cart_item_ids'  => 'nullable|array',
        ]);

        $order = Order::create([
            'user_id'        => $request->user()->id,
            'order_number'   => 'ORD-' . rand(100000, 999999),
            'address'        => $request->address,
            'phone'          => $request->phone,
            'payment_method' => $request->payment_method,
            'total_price'    => $request->total_price,
            'items'          => $request->items,
        ]);

        // Delete only the checked-out items from the user's cart
        if ($request->has('cart_item_ids') && is_array($request->cart_item_ids)) {
            CartItem::destroy($request->cart_item_ids);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'order'   => $order
        ], 201);
    }
}
