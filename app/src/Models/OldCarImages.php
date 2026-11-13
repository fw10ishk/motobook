<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldCarImages extends Model
{
    protected $table = 'old_car_images';
    protected $fillable = ['old_car_id', 'image_url'];

    public function oldCar()
    {
        return $this->belongsTo(OldCar::class);
    }
}
