<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Mostrar formulario de edicion
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Actualizar perfil del usuario (incluye foto)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'dni' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'cropped_photo' => ['nullable', 'string'], // Base64 de imagen recortada
        ]);

        // Procesar imagen recortada si existe
        if ($request->filled('cropped_photo')) {
            // Eliminar foto anterior si existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Decodificar base64 y guardar
            $imageData = $request->input('cropped_photo');
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            
            $imageName = 'photos/' . uniqid() . '_' . time() . '.png';
            Storage::disk('public')->put($imageName, base64_decode($imageData));
            
            $validated['photo'] = $imageName;
        }

        // Remover cropped_photo del array de validados
        unset($validated['cropped_photo']);

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualizar contrasena
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Contrasena actualizada correctamente.');
    }

    /**
     * Eliminar foto de perfil
     */
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => null]);

        return response()->json(['success' => true, 'message' => 'Foto eliminada correctamente.']);
    }
}
