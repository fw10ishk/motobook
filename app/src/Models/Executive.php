<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Executive extends Model
{
    protected $table = 'executives';

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
        $executive = self::where('name', $name)->first();
        if (!$executive) {
            $executive = self::create(['name' => $name]);
        }
        return $executive;
    }
}
