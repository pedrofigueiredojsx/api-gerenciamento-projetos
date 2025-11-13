<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
        ]);

        $token = $user->createToken("app-token")->plainTextToken;

        return response()->json([
            "message" => "Usuário criado com sucesso",
            "user" => $user,
            "token" => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
            "password" => "required|string",
        ]);

        $user = User::where("email", $validated["email"])->first();

        if (!$user || !Hash::check($validated["password"], $user->password)) {
            throw ValidationException::withMessages([
                "email" => ["Credenciais inválidas."],
            ]);
        }

        $token = $user->createToken("app-token")->plainTextToken;

        return response()->json([
            "message" => "Login realizado com sucesso",
            "user" => $user,
            "token" => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(["message" => "Logout realizado com sucesso"]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
