<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tgl_lahir',
        'alamat',
        'no_hp',
        'gaji_per_bulan',
        'foto_ktp',
        'role', // tambahin supaya bisa mass assign role
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tgl_lahir' => 'date',
        ];
    }

    /**
     * Relasi ke Credit.
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    // --- Custom Methods ---

    public function getActiveCredits()
    {
        return $this->credits()->approved()->get();
    }

    public function getPendingCredits()
    {
        return $this->credits()->pending()->get();
    }

    public function getPaidCredits()
    {
        return $this->credits()->paid()->get();
    }

    public function hasOverduePayments(): bool
    {
        return $this->credits()->approved()->get()->some(function ($credit) {
            return $credit->isOverdue();
        });
    }

    public function getTotalDebt()
    {
        return $this->credits()->approved()->get()->sum(function ($credit) {
            return $credit->getSisaPembayaran();
        });
    }

    // --- Role Based Access Helpers ---
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
