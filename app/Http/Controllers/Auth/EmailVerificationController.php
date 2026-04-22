<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Envia el enlace de verificacion por email
     */
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Tu email ya esta verificado.');
        }

        // Generar URL firmada con expiracion de 60 minutos
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Enviar email
        try {
            Mail::send('emails.verify-email', [
                'user' => $user,
                'verificationUrl' => $verificationUrl
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verifica tu correo electronico - Academia');
            });

            return back()->with('success', 'Hemos enviado un enlace de verificacion a tu correo electronico. El enlace expira en 60 minutos.');
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            // Error de conexion SMTP
            Log::error('Error de conexion SMTP: ' . $e->getMessage(), ['userId' => $user->id]);
            
            // En desarrollo, mostrar el link directamente para pruebas
            if (config('app.debug')) {
                return back()
                    ->with('warning', $verificationUrl)
                    ->with('info', 'No se pudo conectar al servidor SMTP. Usa el link de abajo para verificar (solo en modo desarrollo).');
            }
            
            return back()->with('error', 'No se pudo conectar al servidor de correo. Verifica tu conexion a internet o configura correctamente el servidor SMTP.');
        } catch (\Exception $e) {
            Log::error('Error enviando email de verificacion: ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar el correo. Por favor, intenta de nuevo mas tarde.');
        }
    }

    /**
     * Verifica el email usando el token
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verificar que el hash coincida
        if (!hash_equals($hash, sha1($user->email))) {
            return redirect()->route('profile.show')->with('error', 'El enlace de verificacion no es valido.');
        }

        // Verificar que la URL no haya expirado (la firma lo hace automaticamente)
        if (!$request->hasValidSignature()) {
            return redirect()->route('profile.show')->with('error', 'El enlace de verificacion ha expirado. Solicita uno nuevo.');
        }

        // Verificar si ya esta verificado
        if ($user->email_verified_at) {
            return redirect()->route('profile.show')->with('info', 'Tu email ya estaba verificado.');
        }

        // Marcar como verificado
        $user->email_verified_at = Carbon::now();
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Tu correo electronico ha sido verificado exitosamente.');
    }
}
