<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RTO extends Model
{
    protected $table = 'rto';

    protected $fillable = ['name'];

    public $timestamps = false;

    public static function search($query)
    {
        return self::where('name', 'LIKE', "%{$query}%")
            ->select('name')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'text' => $item->name,
                    'value' => $item->name
                ];
            });
    }

    public static function findOrCreateByName($name)
    {
        $rto = self::where('name', $name)->first();
        if (!$rto) {
            $rto = self::create(['name' => $name]);
        }
        return $rto;
    }
}
