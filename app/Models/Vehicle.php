<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merk',
        'model',
        'tahun',
        'harga',
        'deskripsi',
        'stok',
        'gambar_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'tahun' => 'integer',
        'stok' => 'integer'
    ];

    /**
     * Get the credits for the Vehicle.
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'motor_id'); // 'motor_id' is the foreign key in credits table
    }

    // --- Scopes ---

    /**
     * Scope a query to only include available vehicles (stok > 0).
     */
    public function scopeAvailable($query)
    {
        return $query->where('stok', '>', 0);
    }

    /**
     * Scope a query to filter vehicles by merk.
     */
    public function scopeByMerk($query, $merk)
    {
        return $query->where('merk', 'like', "%{$merk}%");
    }

    /**
     * Scope a query to filter vehicles by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    /**
     * Scope a query to filter vehicles by price range.
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('harga', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('harga', '<=', $maxPrice);
        }

        return $query;
    }

    // --- Accessors ---

    /**
     * Get the formatted harga attribute.
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get the full name of the vehicle (merk model tahun).
     */
    public function getFullNameAttribute()
    {
        return "{$this->merk} {$this->model} ({$this->tahun})";
    }

    // --- Custom Methods ---

    /**
     * Check if the vehicle is available (stok > 0).
     */
    public function isAvailable(): bool
    {
        return $this->stok > 0;
    }

    /**
     * Reduce the stock of the vehicle.
     */
    public function reduceStock($quantity = 1): bool
    {
        if ($this->stok >= $quantity) {
            $this->decrement('stok', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase the stock of the vehicle.
     */
    public function increaseStock($quantity = 1): void
    {
        $this->increment('stok', $quantity);
    }
}
