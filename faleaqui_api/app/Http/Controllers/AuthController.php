<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'required|string|max:20|unique:users',
            'birth_date' => 'required|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
            'status' => 1, // Padrão ativo
            'type_user' => $request->type_user ?? 0, // 0: Cliente, 1: Suporte, etc.
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {

        try {
            // Validação das credenciais de login
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);



            // Verifica se as credenciais estão corretas
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Credenciais inválidas'], 401);
            }



            // Obtém o usuário autenticado após a tentativa de login
            $user = Auth::user();


            // Verifique se o usuário foi autenticado corretamente
            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }


            // Gera o token de autenticação para o usuário
            $token = $user->createToken('auth_token')->plainTextToken;

            // Retorna o token de acesso
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
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
