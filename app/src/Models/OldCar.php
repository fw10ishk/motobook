<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldCar extends Model
{
    protected $table = 'old_cars';

    protected $fillable = [
        // Basic Information
        'docket_number',
        'original_price',
        'current_price',

        // Customer Details
        'customer_name',
        'customer_phone',
        'customer_address1',
        'customer_address2',
        'city_name',
        'pincode',
        'email',
        'customer_dob',

        // Vehicle Information
        'manufacturer_name',
        'model_name',
        'color',
        'registration_no',
        'chassis_no',
        'engine_no',
        'insurance_expiry_date',
        'hp_name',
        'manufacture_year',
        'fuel_type',

        // Additional Details
        'dealer_name',
        'location',
        'executive_branch_name',
        'executive_name',
        'insurance_company_name',
        'rto_name',
        'rto_code',
        'parsing_status',
        'number_plate',
        'scheme',
        'broker_name',
        'other_remarks',

        // Documents
        'rc_book',
        'insurance_doc',
        'owner_id_proof',
        'pan_card',
        'aadhar_card',
        'noc',
        'form_35',
        'purchase_agreement',

        // Images
        'main_image'
    ];


    public $timestamps = false;

    public function additionalImages()
    {
        return $this->hasMany(OldCarImages::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class, 'old_car_id');
    }

    public static function createOldCar($attributes)
    {
        // If city_name is provided, ensure it exists in cities table
        if (!empty($attributes['city_name'])) {
            City::findOrCreateByName($attributes['city_name']);
        }

        // If dealer_name is provided, ensure it exists in cities table
        if (!empty($attributes['dealer_name'])) {
            CarDealer::findOrCreateByName($attributes['dealer_name']);
        }

        // If manufacturer_name is provided, ensure it exists in cities table
        if (!empty($attributes['manufacturer_name'])) {
            Manufacturer::findOrCreateByName($attributes['manufacturer_name']);
        }

        // If model_name is provided, ensure it exists in cities table
        if (!empty($attributes['model_name'])) {
            CarModel::findOrCreateByName($attributes['model_name']);
        }

        // If hp_name is provided, ensure it exists in cities table
        if (!empty($attributes['hp_name'])) {
            HirePurchase::findOrCreateByName($attributes['hp_name']);
        }

        // If executive_branch_name is provided, ensure it exists in cities table
        if (!empty($attributes['executive_branch_name'])) {
            ExecutiveBranch::findOrCreateByName($attributes['executive_branch_name']);
        }

        // If executive_name is provided, ensure it exists in cities table
        if (!empty($attributes['executive_name'])) {
            Executive::findOrCreateByName($attributes['executive_name']);
        }

        // If insurance_company_name is provided, ensure it exists in cities table
        if (!empty($attributes['insurance_company_name'])) {
            InsuranceCompany::findOrCreateByName($attributes['insurance_company_name']);
        }

        // If rto_name is provided, ensure it exists in cities table
        if (!empty($attributes['rto_name'])) {
            RTO::findOrCreateByName($attributes['rto_name']);
        }

        // If rto_code is provided, ensure it exists in rto_codes table
        if (!empty($attributes['rto_code'])) {
            RTOCode::findOrCreateByName($attributes['rto_code']);
        }

        $oldCar = self::create($attributes);

        return $oldCar;
    }

    public static function updateOldCar($id, $attributes)
    {
        $oldCar = self::find($id);

        // If city_name is provided, ensure it exists in cities table
        if (!empty($attributes['city_name'])) {
            City::findOrCreateByName($attributes['city_name']);
        }

        // If manufacturer_name is provided, ensure it exists in cities table
        if (!empty($attributes['manufacturer_name'])) {
            Manufacturer::findOrCreateByName($attributes['manufacturer_name']);
        }

        // If model_name is provided, ensure it exists in cities table
        if (!empty($attributes['model_name'])) {
            CarModel::findOrCreateByName($attributes['model_name']);
        }

        // If hp_name is provided, ensure it exists in cities table
        if (!empty($attributes['hp_name'])) {
            HirePurchase::findOrCreateByName($attributes['hp_name']);
        }

        // If executive_branch_name is provided, ensure it exists in cities table
        if (!empty($attributes['executive_branch_name'])) {
            ExecutiveBranch::findOrCreateByName($attributes['executive_branch_name']);
        }

        // If executive_name is provided, ensure it exists in cities table
        if (!empty($attributes['executive_name'])) {
            Executive::findOrCreateByName($attributes['executive_name']);
        }

        // If insurance_company_name is provided, ensure it exists in cities table
        if (!empty($attributes['insurance_company_name'])) {
            InsuranceCompany::findOrCreateByName($attributes['insurance_company_name']);
        }

        // If rto_name is provided, ensure it exists in cities table
        if (!empty($attributes['rto_name'])) {
            RTO::findOrCreateByName($attributes['rto_name']);
        }

        // If rto_code is provided, ensure it exists in rto_codes table
        if (!empty($attributes['rto_code'])) {
            RTOCode::findOrCreateByName($attributes['rto_code']);
        }

        $oldCar->update($attributes);
        return $oldCar;
    }

    public function deleteOldCar($id)
    {
        // Delete OldCarImages
        $additionalImages = $this->additionalImages;
        if ($additionalImages) {
            foreach ($additionalImages as $image) {
                $imagePath = 'uploads/old-cars/' . $image->image_url;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
        }

        // Remove From uploads/old-cars as well
        $mainImage = $this->main_image;
        if ($mainImage) {
            $mainImagePath = 'uploads/old-cars/' . $mainImage;
            if (file_exists($mainImagePath)) {
                unlink($mainImagePath);
            }
        }

        return $this->delete();
    }

    public static function updateStatus($id, $status)
    {
        $newCar = self::find($id);
        $newCar->status = $status;
        $newCar->save();
        return $newCar;
    }
}
