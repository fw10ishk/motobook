<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RememberToken extends Model
{
    protected $table = 'remember_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'is_valid',
    ];

    public static function createToken($userId, $token, $expiresAt)
    {
        return self::create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
            'is_valid' => true,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
