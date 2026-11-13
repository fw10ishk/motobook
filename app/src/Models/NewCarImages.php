<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewCarImages extends Model
{
    protected $table = 'new_car_images';
    protected $fillable = ['new_car_id', 'image_url'];

    public function newCar()
    {
        return $this->belongsTo(NewCar::class);
    }
}
