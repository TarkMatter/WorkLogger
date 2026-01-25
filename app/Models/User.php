<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // role定義
    public const ROLE_MEMBER = 'member';
    public const ROLE_APPROVER = 'approver';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',// 役割
    ];

    
    // role判定
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canApprove(): bool
    {
        return in_array($this->role, ['admin', 'approver'], true);
    }

    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class)->withTimestamps();
    }

    public function hasPermission(string $key): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // すでに permissions をロードしているならメモリで判定（高速）
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(fn ($p) => $p->key === $key);
        }

        // 未ロードならDBで判定（最新状態）
        return $this->permissions()->where('key', $key)->exists();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
