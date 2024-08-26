<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Importa a facade HTTP do Laravel

class OrdersController extends Controller
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
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:12048', // Validação da imagem
            ]);

            $order = Orders::create([
                'title' => $request->title,
                'obs' => $request->obs,
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'altitude' => $request->altitude,
                'status' => $request->status,
            ]);

            // Verifica se uma imagem foi enviada
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('photos', 'public'); // Salva a imagem no diretório 'storage/app/public/photos'

                // Salva a referência da imagem no banco de dados
                Photo::create([
                    'order_id' => $order->id,
                    'photo' => $photoPath,
                ]);
            }

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
        $order = Orders::findOrFail($id);

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

    public function index()
    {
        try {
            // Obtém o usuário autenticado
            $user = Auth::user();

            // Verifica o tipo de usuário
            if ($user->type_user == 1) {
                // Se for type_user = 1, exibe apenas as ordens do usuário logado
                $orders = Orders::select('orders.*', 'users.name as user_name')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->where('orders.user_id', $user->id)
                    ->with('photos')
                    ->get();
            } else {
                // Se for type_user = 2, exibe todas as ordens
                $orders = Orders::select('orders.*', 'users.name as user_name')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->with('photos')
                    ->get();
            }

            // Itera sobre as ordens e faz a chamada à API Nominatim
            foreach ($orders as $order) {
                $latitude = $order->latitude;
                $longitude = $order->altitude;

                // Chamada à API Nominatim com timeout
                $response = Http::timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $latitude,
                    'lon' => $longitude
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Verifica se a chave 'address' está presente na resposta
                    if (isset($data['address'])) {
                        $address = $data['address'];
                        $order->road = $address['road'] ?? null;
                        $order->suburb = $address['suburb'] ?? null;
                        $order->postcode = $address['postcode'] ?? null;
                    } else {
                        // Se 'address' não estiver presente
                        $order->road = null;
                        $order->suburb = null;
                        $order->postcode = null;
                    }
                } else {
                    // Define os campos como nulos se a solicitação falhar
                    $order->road = null;
                    $order->suburb = null;
                    $order->postcode = null;
                }
            }

            // Adiciona a URL da imagem às fotos
            $orders->transform(function ($order) {
                $order->photos->transform(function ($photo) {
                    $photo->photo_url = url('storage/' . $photo->photo); // Gera a URL completa da imagem
                    return $photo;
                });
                return $order;
            });

            // Retorna as ordens com as informações adicionais de localização
            return response()->json(['orders' => $orders], 200);

        } catch (\Exception $e) {
            // Captura o erro e retorna a mensagem de erro e o código da linha
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }


}
