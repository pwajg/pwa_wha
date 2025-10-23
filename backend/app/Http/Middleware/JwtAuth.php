<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'message' => 'Token de acceso requerido'
            ], 401);
        }
        
        $payload = $this->verifyJWT($token);
        
        if (!$payload) {
            return response()->json([
                'message' => 'Token inválido o expirado',
                'debug' => [
                    'token_received' => $token,
                    'token_parts' => explode('.', $token),
                    'app_key_exists' => !empty(config('app.key'))
                ]
            ], 401);
        }
        
        // Agregar información del usuario al request
        $request->merge(['user_id' => $payload['sub']]);
        $request->merge(['user_email' => $payload['email']]);
        $request->merge(['user_rol' => $payload['rol']]);
        
        return $next($request);
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
}
