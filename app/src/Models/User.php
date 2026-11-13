<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'full_name',
        'status',
        'role',
        'last_login',
    ];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value)
    {
        var_dump("Function Called");
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }

    public static function createUser($email, $password, $fullName = null, $role = 'user')
    {
        return self::create([
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName,
            'role' => $role,
            'status' => 'active',
        ]);
    }

    public static function loginUser($email, $password, $rememberMe = false)
    {
        $user = self::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            return null;
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return null;
        }

        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // If remember me is checked, generate a remember token
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));  // Generate a random token
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 week'));  // Set expiry for 1 week

            // Create or update the token in the RememberToken model
            RememberToken::createToken($user->id, $token, $expiresAt);

            // Set the token in a cookie
            setcookie('remember_token', $token, strtotime('+1 week'), '/', '', false, true);
            return ['user' => $user, 'remember_token' => $token];
        }

        return ['user' => $user];
    }

    public static function checkRememberToken($token)
    {
        // Check the token validity
        $tokenRecord = RememberToken::where('token', $token)
            ->where('is_valid', true)
            ->where('expires_at', '>', date('Y-m-d H:i:s'))
            ->first();

        if ($tokenRecord) {
            return User::find($tokenRecord->user_id);
        }

        return null;
    }

    public function rememberTokens()
    {
        return $this->hasMany(RememberToken::class);
    }

    public static function changePassword($oldPassword, $newPassword)
    {
        $user = self::find($_SESSION['user']['id']);

        if (!password_verify($oldPassword, $user->password)) {
            return false;
        }

        $user->password = $newPassword;
        $user->save();

        return true;
    }

    public static function updateUser($id, $attributes)
    {
        $user = self::find($id);

        if (!$user) {
            return false;
        }

        // Update password if provided
        if (!empty($attributes['password'])) {
            $user->password = $attributes['password'];
        }

        // Update other fields
        $user->fill([
            'full_name' => $attributes['full_name'],
            'role' => $attributes['role'],
            'status' => $attributes['status'] ? 'active' : 'inactive'
        ]);

        return $user->save();
    }
}
