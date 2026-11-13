<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'models';

    protected $fillable = ['name'];

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
        $model = self::where('name', $name)->first();
        if (!$model) {
            $model = self::create(['name' => $name]);
        }
        return $model;
    }

    public $timestamps = false;
}
