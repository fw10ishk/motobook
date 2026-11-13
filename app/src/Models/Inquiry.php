<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\FullPayment;

class Inquiry extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'vehicle_of_interest',
        'preferred_contact_method',
        'additional_notes',
        'inquiry_source',
        'referral_person',
        'status'
    ];

    public static function createInquiry($attributes)
    {
        return self::create($attributes);
    }

    public static function updateInquiry($id, $attributes)
    {
        $inquiry = self::find($id);
        $inquiry->update($attributes);

        return $inquiry;
    }

    public static function deleteInquiry($id)
    {
        $inquiry = self::find($id);
        $inquiry->delete();

        return $inquiry;
    }

    public static function changeStatus($id)
    {
        $inquiry = self::find($id);

        $status = $inquiry->status == 'completed' ? 'pending' : 'completed';

        $inquiry->update(['status' => $status]);

        return $inquiry;
    }
}
