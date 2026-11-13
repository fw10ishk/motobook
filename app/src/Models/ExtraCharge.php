<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    protected $table = 'extra_charges';

    protected $fillable = [
        'id',
        'old_car_id',
        'description',
        'amount'
    ];

    public function oldCar()
    {
        return $this->belongsTo(OldCar::class, 'old_car_id');
    }

    public $timestamps = false;

    public static function getExtraCharges($oldCarId)
    {
        return self::where('old_car_id', $oldCarId)->get();
    }

    public static function addExtraCharge($oldCarId, $description, $amount)
    {
        $extraCharge = new self();
        $extraCharge->old_car_id = $oldCarId;
        $extraCharge->description = $description;
        $extraCharge->amount = $amount;
        $extraCharge->save();
    }

    public static function deleteExtraCharge($id)
    {
        self::where('id', $id)->delete();
    }
}
