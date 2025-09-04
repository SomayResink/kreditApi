<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'jumlah_pinjaman',
        'dp',
        'tenor',
        'cicilan_per_bulan',
        'total_bayar',
        'status',
        'tanggal_pengajuan'
    ];

    protected $casts = [
        'jumlah_pinjaman'   => 'decimal:2',
        'dp'                => 'decimal:2',
        'cicilan_per_bulan' => 'decimal:2',
        'total_bayar'       => 'decimal:2',
        'tenor'             => 'integer',
        'tanggal_pengajuan' => 'date',
    ];

    // =======================
    // 🔗 RELATIONSHIPS
    // =======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    // Alias kalau masih ada yang pakai 'motor'
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

      // Method buat update status kredit otomatis
    public function updateStatusBasedOnPayments()
    {
        $totalPaid = $this->payments()->sum('jumlah_bayar');

        if ($totalPaid >= $this->getOriginal('total_bayar')) {
            $this->status = 'paid';
        } else {
            $this->status = 'approved';
        }

        $this->save();
    }

    // =======================
    // 📌 SCOPES
    // =======================

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // =======================
    // 🔧 CUSTOM METHODS
    // =======================

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
        // kurangi stok kendaraan setelah disetujui
        if ($this->vehicle) {
            $this->vehicle->reduceStock();
        }
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    // =======================
    // 📌 ACCESSORS
    // =======================

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_bayar - $this->payments()->sum('jumlah_bayar'));
    }

    public function getProgressAttribute(): float
    {
        if ($this->total_bayar == 0) {
            return 0;
        }

        return ($this->payments()->sum('jumlah_bayar') / $this->total_bayar) * 100;
    }
}
