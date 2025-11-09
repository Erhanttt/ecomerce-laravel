<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 🧾 Merr të gjitha porositë (për adminin)
     */
    public function index()
    {
        $orders = Order::with('items.product')->latest()->get();
        return response()->json($orders);
    }

    /**
     * 🛒 Krijon një porosi të re
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'country'      => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'description'  => 'nullable|string',
            'total_price'  => 'required|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 🧩 Krijo porosinë
            $order = Order::create([
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'phone'       => $validated['phone'],
                'email'       => $validated['email'] ?? null,
                'country'     => $validated['country'],
                'address'     => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'description' => $validated['description'] ?? null,
                'total_price' => $validated['total_price'],
                'status'      => 'pending',
            ]);

            // 🛍️ Shto produktet e porosisë
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Porosia u krijua me sukses!',
                'order'   => $order->load('items.product')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gabim gjatë ruajtjes së porosisë!',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,delivered'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Statusi u përditësua me sukses!',
            'order' => $order
        ]);
    }

    /**
     * 📄 Shfaq detajet e një porosie
     */
    public function show($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        
        return response()->json($order);
    }

    /**
     * ❌ Fshij një porosi
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Porosia u fshi me sukses.']);
    }
}
