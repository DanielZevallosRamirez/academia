<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    // ==================== RELACIONES ====================

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // ==================== HELPERS ====================

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }
}
