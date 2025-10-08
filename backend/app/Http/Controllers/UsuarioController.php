<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $usuario = Usuario::where('email', $validated['email'])->first();
        
        if(!$usuario || !Hash::check($validated['password'], $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales Incorrectas'
            ], 401);
        }
        
        // Generar token JWT simple
        $token = $this->generateJWT($usuario);
        
        $usuario->load('sucursal');
        
        return response()->json([
            'message' => 'Login Exitoso',
            'usuario' => $usuario,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600 // 1 hora
        ], 200);
    }
    private function generateJWT($usuario) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'sub' => $usuario->id,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'iat' => time(),
            'exp' => time() + 3600 // 1 hora
        ]);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, config('app.key'), true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
    
    /**
     * Verificar token JWT
     */
    private function verifyJWT($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];
        
        $expectedSignature = hash_hmac('sha256', $header . "." . $payload, config('app.key'), true);
        $expectedBase64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
        
        if ($signature !== $expectedBase64Signature) {
            return false;
        }
        
        $decodedPayload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
        
        if ($decodedPayload['exp'] < time()) {
            return false;
        }
        
        return $decodedPayload;
    }

    public function logout(Request $request) {
        return response()->json([
            'message' => 'Logout Exitoso'
        ], 200);
    }
    
    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request) {
        $usuario = Usuario::with('sucursal')->find($request->user_id);
        
        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        
        return response()->json([
            'usuario' => $usuario
        ], 200);
    }
    
    public function index()
    {
        return Usuario::with('sucursal')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:40|unique:usuarios',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:Administrador,Colaborador',
            'idSucursal' => 'required|exists:sucursales,id',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $usuario = Usuario::create($validated);
        return response()->json($usuario,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
