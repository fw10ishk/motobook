<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

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
        $name = trim($name);
        $city = self::where('name', $name)->first();

        if (!$city && !empty($name)) {
            $city = self::create(['name' => $name]);
        }

        return $city;
    }
}
