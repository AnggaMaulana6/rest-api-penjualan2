<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Http\Resources\OrderDetailResource;
use App\Models\Product;

class OrderController extends Controller
{
    public function index() {
        $orders = Order::all();
        return OrderDetailResource::collection($orders->loadMissing('product:id,product_name,price,stock', 'customer:id,name,phone'));
    }

    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
        ]);
    
        // Ambil produk terkait dari relasi
        $product = Product::findOrFail($request->product_id);
    
        // Hitung jumlah pembayaran
        $payment = $request->quantity * $product->price;
    
        // Buat entitas Order
        $order = Order::create([
            'customer_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'payment' => $payment,
            'status' => 'order'
        ]);
    
        // Load relasi yang diperlukan
        $order->loadMissing('product:id,product_name,price,stock', 'customer:id,name,phone');
    
        return new OrderDetailResource($order);

    }
    public function checkout(Request $request, $id){
        // Ambil order berdasarkan ID
        $order = Order::findOrFail($id);

        // Pastikan order belum di-checkout sebelumnya
        if ($order->status === 'checkout') {
            return response()->json(['message' => 'Order sudah di-checkout sebelumnya.'], 400);
        }

        // Saat proses checkout, buat entitas Checkout
        $checkout = Checkout::create([
            'order_id' => $order->id,
            'product_id' => $order->product_id,
            'customer_id' => $order->customer_id,
            'payment_method' => $request->payment_method,
            'total_amount' => $order->payment,
        ]);

        // Perbarui status order menjadi 'checkout'
        $order->update(['status' => 'checkout']);
        return new OrderDetailResource($order->loadMissing('product:id,product_name,price,stock', 'customer:id,name,phone'));

    }


    public function update(Request $request, $id) {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());

        return new OrderDetailResource($order->loadMissing('product:id,product_name,price,stock', 'customer:id,name,phone'));

    }

    public function destroy($id) {
        $order = Order::findOrFail($id);
        $order->delete();
        
        return response()->json(['data berhasil dihapus']);

    }
    public function destroyCheckout($id) {
        $checkout = Checkout::findOrFail($id);
        $checkout->delete();
        
        return response()->json(['data checkout berhasil dihapus']);

    }
}
