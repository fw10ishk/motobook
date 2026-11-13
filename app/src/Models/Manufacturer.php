<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = 'manufacturers';

    protected $fillable = ['name'];

    // No need to allow create, update, delete operations
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
        $manufacturer = self::where('name', $name)->first();
        if (!$manufacturer) {
            $manufacturer = self::create(['name' => $name]);
        }
        return $manufacturer;
    }
}
