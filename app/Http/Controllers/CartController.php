<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(Request $request)
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function cartData($cart)
    {
        $cart->load('items.product');

        $validItems = $cart->items->filter(fn($i) => $i->product !== null);

        return [
            'id'          => $cart->id,
            'total_items' => $validItems->count(),
            'total_price' => $validItems->sum(fn($i) => $i->quantity * $i->product->price),
            'items'       => $validItems->map(fn($i) => [
                'id'       => $i->id,
                'quantity' => $i->quantity,
                'product'  => ['id' => $i->product->id, 'title' => $i->product->title, 'price' => $i->product->price],
            ])->values(),
        ];
    }

    public function index(Request $request)
    {
        return response()->json($this->cartData($this->getCart($request)));
    }

    public function addItem(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'required|integer|min:1']);
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $cart = $this->getCart($request);
        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $request->product_id)->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            CartItem::create(['cart_id' => $cart->id, 'product_id' => $request->product_id, 'quantity' => $request->quantity]);
        }

        return response()->json(['message' => 'Item added', 'cart' => $this->cartData($cart)]);
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = $this->getCart($request);
        CartItem::where('id', $itemId)->where('cart_id', $cart->id)->firstOrFail()->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Item updated', 'cart' => $this->cartData($cart)]);
    }

    public function removeItem(Request $request, $itemId)
    {
        $cart = $this->getCart($request);
        CartItem::where('id', $itemId)->where('cart_id', $cart->id)->firstOrFail()->delete();
        return response()->json(['message' => 'Item removed', 'cart' => $this->cartData($cart)]);
    }
}
