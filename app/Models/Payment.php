<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon; // Import Carbon for date manipulation

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'credit_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode',
        'bukti_url',
        'tenor_ke',
        'status',
        'denda',
        
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'float' // Cast to decimal with 2 places
    ];

    /**
     * Get the credit that owns the Payment.
     */
    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    // --- Scopes ---

    /**
     * Scope a query to filter payments by method.
     */
    public function scopeByMetode($query, $metode)
    {
        return $query->where('metode', $metode);
    }

    /**
     * Scope a query to filter payments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include late payments.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope a query to only include cash payments.
     */
    public function scopeCash($query)
    {
        return $query->where('metode', 'cash');
    }

    /**
     * Scope a query to only include transfer payments.
     */
    public function scopeTransfer($query)
    {
        return $query->where('metode', 'transfer');
    }

    /**
     * Scope a query to filter payments within a date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include payments from the current month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_bayar', Carbon::now()->month)
                    ->whereYear('tanggal_bayar', Carbon::now()->year);
    }

    // --- Accessors ---

    /**
     * Get the formatted jumlah_bayar attribute.
     */
    public function getFormattedJumlahBayarAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Get the formatted tanggal_bayar attribute.
     */
    public function getFormattedTanggalBayarAttribute()
    {
        return Carbon::parse($this->tanggal_bayar)->format('d M Y');
    }

    // --- Custom Methods ---

    /**
     * Check if the payment is late.
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Mark the payment as late.
     */
    public function markAsLate(): void
    {
        $this->update(['status' => 'late']);
    }

    /**
     * Get the payment number (e.g., 1st payment, 2nd payment).
     * This assumes payments are ordered by date for a given credit.
     */
    public function getPaymentNumber()
    {
        // This requires the credit relationship to be loaded
        if ($this->credit) {
            return $this->credit->payments()
                        ->where('tanggal_bayar', '<=', $this->tanggal_bayar)
                        ->count();
        }
        return null;
    }

    /**
     * The "booting" method of the model.
     * Used to register model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update credit status after payment creation, update, or deletion
        static::created(function ($payment) {
            $payment->credit->updateStatusBasedOnPayments();
        });

        static::updated(function ($payment) {
            $payment->credit->updateStatusBasedOnPayments();
        });

        static::deleted(function ($payment) {
            $payment->credit->updateStatusBasedOnPayments();
        });
    }
}
