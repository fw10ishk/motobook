<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDealer extends Model
{
    protected $table = 'car_dealers';

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
        $hp = self::where('name', $name)->first();
        if (!$hp) {
            $hp = self::create(['name' => $name]);
        }
        return $hp;
    }
}
