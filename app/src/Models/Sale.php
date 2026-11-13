<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\FullPayment;

class Sale extends Model
{
    protected $fillable = [
        'car_type',
        'car_id',
        'sale_type',
        'total_price',
        'sale_date',
        'buyer_name'
    ];

    public function fullPayment()
    {
        return $this->hasOne(FullPayment::class);
    }

    public function dealerFullPayment()
    {
        return $this->hasOne(DealerFullPayment::class);
    }

    public function oldCarBuyer()
    {
        return $this->hasOne(OldCarBuyer::class);
    }

    public static function createSale($attributes)
    {
        $sale = self::create($attributes);

        if ($attributes['sale_type'] === 'full') {
            FullPayment::createFullPayment([
                'sale_id' => $sale->id,
                'total_price' => $attributes['total_price']
            ]);
        }

        return $sale;
    }
}
