<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldCarBuyer extends Model
{
    protected $fillable = [
        'sale_id',
        'name',
        'contact',
        'address_line_1',
        'address_line_2',
        'city_name',
        'pincode',
        'email',
        'dob',
        'pan_card',
        'aadhar_card',
        'driving_license',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public static function createBuyer($attributes)
    {
        return self::create($attributes);
    }

    public static function updateBuyer($id, $attributes)
    {
        return self::where('id', $id)->update($attributes);
    }

    public static function deleteBuyer($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getBuyer($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getBuyers($saleId)
    {
        return self::where('sale_id', $saleId)->get();
    }
}
