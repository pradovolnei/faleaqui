<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'obs' => 'nullable|string',
                'latitude' => 'nullable|string',
                'altitude' => 'nullable|string',
                'status' => 'required|string|max:20',
            ]);

            $order = Order::create([
                'title' => $request->title,
                'obs' => $request->obs,
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'altitude' => $request->altitude,
                'status' => $request->status,
            ]);

            return response()->json(['order' => $order], 201);
        } catch (\Exception $e) {
            // Captura o erro e retorna a mensagem de erro e o código da linha
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Ação não permitida'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'obs' => 'nullable|string',
            'latitude' => 'nullable|string',
            'altitude' => 'nullable|string',
            'status' => 'sometimes|string|max:20',
        ]);

        $order->update($request->all());

        return response()->json(['order' => $order], 200);
    }
}
