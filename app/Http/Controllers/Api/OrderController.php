<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); 
        
        $query = Order::query();

        // Filter by order status ('pending', 'completed')
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $user = User::find($request->user_id);
        \DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $request->user_id,
                'total_amount' => $request->total_amount,
                'status' => OrderStatus::Pending,
            ]);

            $items = $request->items;
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price, 
                    'total' => $product->price * ($item['quantity'] * 1), 
                ]);
            }

            $order->shipping()->create([
                 'recipient_name' => @$request->shipping['recipient_name'] || $user->name,
                 'address_line1' => $request->shipping['address_line1'],
                 'address_line2' => $request->shipping['address_line2'],
                 'city' => $request->shipping['city'],
                 'state' => $request->shipping['state'],
                 'postal_code' => $request->shipping['postal_code'],
                 'country' => $request->shipping['country'] ?: 'Sri Lanka',
                 'phone' => $request->shipping['phone'],
                 'shipping_cost' => 100.00,
                 'tracking_number' => \Str::upper(\Str::ulid()),
            ]);
            $payment = $order->payment()->create($request->payment);

            \DB::commit();
            return response()->json(['error' => FALSE, 'message' => 'Order created successfully', 'order' => $order], 201);
        } 
        catch (\Throwable $th) {
            throw $th;
            \DB::rollBack();
            return response()->json(['error' => TRUE, 'message' => $th>getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['items', 'shipping', 'payment'])->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => TRUE, 'message' => 'Order not found'], 404);
        }
        
        return response()->json($order, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        $order = Order::find($id);
        \DB::beginTransaction();
        try {
            if ($request->status === OrderStatus::Processing) {
                $order = OrderStatus::Processing;
            }

            $order->save();
            \DB::commit();

            return response()->json(['error' => FALSE, 'message' => 'Order updated successfully', 'order' => $order], 201);
        } 
        catch (\Throwable $th) {
            //throw $th;
            \DB::rollBack();
            return response()->json(['error' => TRUE, 'message' => $th>getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::find($id);
        \DB::beginTransaction();
        try {
            $order->delete();
            \DB::commit();

            return response()->json(['error' => FALSE, 'message' => 'Order updated successfully', 'order' => $order], 201);
        } 
        catch (\Throwable $th) {
            //throw $th;
            \DB::rollBack();
            return response()->json(['error' => TRUE, 'message' => $th>getMessage()], 500);
        }
    }
}
