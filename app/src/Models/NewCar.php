<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\FileUpload;

class NewCar extends Model
{
    protected $table = 'new_cars';

    protected $fillable = [
        'id',
        'docket_number',
        'entry_type',
        'booking_date',
        'delivery_date',
        'customer_name',
        'customer_phone',
        'customer_address1',
        'customer_address2',
        'city_name',
        'pincode',
        'email',
        'customer_dob',
        'nominee_relationship',
        'nominee_name',
        'nominee_dob',
        'manufacturer_name',
        'model_name',
        'color',
        'registration_no',
        'chassis_no',
        'engine_no',
        'dealer_name',
        'location',
        'hp_name',
        'manufacture_year',
        'fuel_type',
        'executive_branch_name',
        'executive_name',
        'insurance_company_name',
        'rto_name',
        'rto_code',
        'parsing_status',
        'number_plate',
        'choice_number',
        'scheme',
        'broker_name',
        'other_remarks',
        'kyc',
        'deal_agreement',
        'main_image',
        'rto_tax',
        'insurance',
        'amc_gmc_tax',
        'ext_warranty',
        'accessories',
        'krunal_accessories',
        'fast_tag',
        'tcs',
        'loyalty_card',
        'driver_price',
        'total_on_road',
        'consumer_offer',
        'corporate_discount',
        'other_discount',
        'net_on_road',
        'disbursement',
        'down_payment',
        'net_short_payment',
        'ex_showroom_price',
        "dealer_ex_showroom_price",
        "dealer_rto_tax",
        "dealer_insurance",
        "dealer_amc_gmc_tax",
        "dealer_ext_warranty",
        "dealer_accessories",
        "dealer_krunal_accessories",
        "dealer_fast_tag",
        "dealer_tcs",
        "dealer_loyalty_card",
        'dealer_driver_price',
        "dealer_total_on_road",
        "dealer_consumer_offer",
        "dealer_corporate_discount",
        "dealer_other_discount",
        "dealer_net_on_road",
        "dealer_disbursement",
        "dealer_down_payment",
        "dealer_net_short_payment",
    ];

    public $timestamps = false;

    public function additionalImages()
    {
        return $this->hasMany(NewCarImages::class);
    }

    public static function createNewCar($attributes, $additionalImages)
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

        $newCar = self::create($attributes);

        // Handle additional images
        $additionalImagesCount = 0;
        foreach ($additionalImages as $image) {
            if ($additionalImagesCount >= 4) break;

            if ($image->getError() === UPLOAD_ERR_OK) {
                try {
                    $imageUrl = FileUpload::upload($image, 'uploads/new-cars');
                    NewCarImages::create([
                        'new_car_id' => $newCar->id,
                        'image_url' => $imageUrl,
                    ]);
                    $additionalImagesCount++;
                } catch (\Exception $e) {
                    // Log error, continue with next image
                    error_log('Additional image upload failed: ' . $e->getMessage());
                }
            }
        }

        return $newCar;
    }

    public static function updateNewCar($id, $attributes)
    {
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

        $newCar = self::find($id);
        $newCar->update($attributes);
        return $newCar;
    }

    public function deleteNewCar($id)
    {
        // Delete NewCarImages
        $additionalImages = $this->additionalImages;
        if ($additionalImages) {
            foreach ($additionalImages as $image) {
                $imagePath = 'uploads/new-cars/' . $image->image_url;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
        }

        // Remove From uploads/new-cars as well
        $mainImage = $this->main_image;
        if ($mainImage) {
            $mainImagePath = 'uploads/new-cars/' . $mainImage;
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
