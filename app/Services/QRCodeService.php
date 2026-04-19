<?php

namespace App\Services;

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QRCodeService
{
    /**
     * Genera un codigo QR unico para un estudiante
     */
    public function generateForStudent(User $student): string
    {
        // Generar codigo unico si no existe
        if (!$student->qr_code) {
            $student->qr_code = $this->generateUniqueCode($student);
            $student->save();
        }

        return $student->qr_code;
    }

    /**
     * Genera un codigo unico basado en el estudiante
     */
    protected function generateUniqueCode(User $student): string
    {
        $prefix = 'STU';
        $timestamp = now()->format('ymd');
        $random = strtoupper(Str::random(4));
        $id = str_pad($student->id, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$timestamp}-{$id}-{$random}";
    }

    /**
     * Genera la imagen QR en formato SVG
     */
    public function generateQRImage(string $code, int $size = 300): string
    {
        return QrCode::size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($code);
    }

    /**
     * Genera la imagen QR en formato PNG (base64)
     */
    public function generateQRImageBase64(string $code, int $size = 300): string
    {
        $qr = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($code);

        return 'data:image/png;base64,' . base64_encode($qr);
    }

    /**
     * Valida si un codigo QR pertenece a un estudiante activo
     */
    public function validateCode(string $code): ?User
    {
        return User::where('qr_code', $code)
            ->where('role', 'estudiante')
            ->where('status', 'activo')
            ->first();
    }

    /**
     * Regenera el codigo QR de un estudiante
     */
    public function regenerateCode(User $student): string
    {
        $student->qr_code = $this->generateUniqueCode($student);
        $student->save();

        return $student->qr_code;
    }

    /**
     * Obtiene la URL del QR para mostrar
     */
    public function getQRUrl(User $student): string
    {
        return route('students.qr-code', $student);
    }
}
