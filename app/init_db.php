<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;

$schema = Capsule::schema();

echo "Initializing database tables..." . PHP_EOL;

// 1. users
if (!$schema->hasTable('users')) {
    $schema->create('users', function ($table) {
        $table->bigIncrements('id');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('full_name')->nullable();
        $table->string('status')->default('active');
        $table->string('role')->default('admin');
        $table->dateTime('last_login')->nullable();
        $table->timestamps();
    });
    echo "Created 'users' table." . PHP_EOL;
}

// 2. remember_tokens
if (!$schema->hasTable('remember_tokens')) {
    $schema->create('remember_tokens', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('user_id');
        $table->string('token');
        $table->dateTime('expires_at');
        $table->boolean('is_valid')->default(true);
        $table->timestamps();
    });
    echo "Created 'remember_tokens' table." . PHP_EOL;
}

// 3. Lookup tables
$lookupTables = [
    'cities', 'car_dealers', 'models', 'manufacturers', 
    'rto', 'rto_codes', 'hire_purchase', 'insurance_companies', 
    'executives', 'executive_branches'
];

foreach ($lookupTables as $t) {
    if (!$schema->hasTable($t)) {
        $schema->create($t, function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
        echo "Created '$t' table." . PHP_EOL;
    }
}

// 4. inquiries
if (!$schema->hasTable('inquiries')) {
    $schema->create('inquiries', function ($table) {
        $table->bigIncrements('id');
        $table->string('customer_name')->nullable();
        $table->string('customer_email')->nullable();
        $table->string('customer_phone')->nullable();
        $table->string('vehicle_of_interest')->nullable();
        $table->string('preferred_contact_method')->nullable();
        $table->text('additional_notes')->nullable();
        $table->string('inquiry_source')->nullable();
        $table->string('referral_person')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();
    });
    echo "Created 'inquiries' table." . PHP_EOL;
}

// 5. new_cars
if (!$schema->hasTable('new_cars')) {
    $schema->create('new_cars', function ($table) {
        $table->bigIncrements('id');
        $table->string('docket_number')->nullable();
        $table->string('entry_type')->nullable();
        $table->string('booking_date')->nullable();
        $table->string('delivery_date')->nullable();
        $table->string('customer_name')->nullable();
        $table->string('customer_phone')->nullable();
        $table->text('customer_address1')->nullable();
        $table->text('customer_address2')->nullable();
        $table->string('city_name')->nullable();
        $table->string('pincode')->nullable();
        $table->string('email')->nullable();
        $table->string('customer_dob')->nullable();
        $table->string('nominee_relationship')->nullable();
        $table->string('nominee_name')->nullable();
        $table->string('nominee_dob')->nullable();
        $table->string('manufacturer_name')->nullable();
        $table->string('model_name')->nullable();
        $table->string('color')->nullable();
        $table->string('registration_no')->nullable();
        $table->string('chassis_no')->nullable();
        $table->string('engine_no')->nullable();
        $table->string('dealer_name')->nullable();
        $table->string('location')->nullable();
        $table->string('hp_name')->nullable();
        $table->string('manufacture_year')->nullable();
        $table->string('fuel_type')->nullable();
        $table->string('executive_branch_name')->nullable();
        $table->string('executive_name')->nullable();
        $table->string('insurance_company_name')->nullable();
        $table->string('rto_name')->nullable();
        $table->string('rto_code')->nullable();
        $table->string('parsing_status')->nullable();
        $table->string('number_plate')->nullable();
        $table->string('choice_number')->nullable();
        $table->string('scheme')->nullable();
        $table->string('broker_name')->nullable();
        $table->text('other_remarks')->nullable();
        $table->string('kyc')->nullable();
        $table->string('deal_agreement')->nullable();
        $table->string('main_image')->nullable();
        $table->string('rto_tax')->nullable();
        $table->string('insurance')->nullable();
        $table->string('amc_gmc_tax')->nullable();
        $table->string('ext_warranty')->nullable();
        $table->string('accessories')->nullable();
        $table->string('krunal_accessories')->nullable();
        $table->string('fast_tag')->nullable();
        $table->string('tcs')->nullable();
        $table->string('loyalty_card')->nullable();
        $table->string('driver_price')->nullable();
        $table->string('total_on_road')->nullable();
        $table->string('consumer_offer')->nullable();
        $table->string('corporate_discount')->nullable();
        $table->string('other_discount')->nullable();
        $table->string('net_on_road')->nullable();
        $table->string('disbursement')->nullable();
        $table->string('down_payment')->nullable();
        $table->string('net_short_payment')->nullable();
        $table->string('ex_showroom_price')->nullable();
        $table->string('dealer_ex_showroom_price')->nullable();
        $table->string('dealer_rto_tax')->nullable();
        $table->string('dealer_insurance')->nullable();
        $table->string('dealer_amc_gmc_tax')->nullable();
        $table->string('dealer_ext_warranty')->nullable();
        $table->string('dealer_accessories')->nullable();
        $table->string('dealer_krunal_accessories')->nullable();
        $table->string('dealer_fast_tag')->nullable();
        $table->string('dealer_tcs')->nullable();
        $table->string('dealer_loyalty_card')->nullable();
        $table->string('dealer_driver_price')->nullable();
        $table->string('dealer_total_on_road')->nullable();
        $table->string('dealer_consumer_offer')->nullable();
        $table->string('dealer_corporate_discount')->nullable();
        $table->string('dealer_other_discount')->nullable();
        $table->string('dealer_net_on_road')->nullable();
        $table->string('dealer_disbursement')->nullable();
        $table->string('dealer_down_payment')->nullable();
        $table->string('dealer_net_short_payment')->nullable();
        $table->string('status')->default('available');
        $table->timestamps();
    });
    echo "Created 'new_cars' table." . PHP_EOL;
}

// 6. new_car_images
if (!$schema->hasTable('new_car_images')) {
    $schema->create('new_car_images', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('new_car_id');
        $table->string('image_url');
        $table->timestamps();
    });
    echo "Created 'new_car_images' table." . PHP_EOL;
}

// 7. old_cars
if (!$schema->hasTable('old_cars')) {
    $schema->create('old_cars', function ($table) {
        $table->bigIncrements('id');
        $table->string('docket_number')->nullable();
        $table->string('original_price')->nullable();
        $table->string('current_price')->nullable();
        $table->string('customer_name')->nullable();
        $table->string('customer_phone')->nullable();
        $table->text('customer_address1')->nullable();
        $table->text('customer_address2')->nullable();
        $table->string('city_name')->nullable();
        $table->string('pincode')->nullable();
        $table->string('email')->nullable();
        $table->string('customer_dob')->nullable();
        $table->string('manufacturer_name')->nullable();
        $table->string('model_name')->nullable();
        $table->string('color')->nullable();
        $table->string('registration_no')->nullable();
        $table->string('chassis_no')->nullable();
        $table->string('engine_no')->nullable();
        $table->string('insurance_expiry_date')->nullable();
        $table->string('hp_name')->nullable();
        $table->string('manufacture_year')->nullable();
        $table->string('fuel_type')->nullable();
        $table->string('dealer_name')->nullable();
        $table->string('location')->nullable();
        $table->string('executive_branch_name')->nullable();
        $table->string('executive_name')->nullable();
        $table->string('insurance_company_name')->nullable();
        $table->string('rto_name')->nullable();
        $table->string('rto_code')->nullable();
        $table->string('parsing_status')->nullable();
        $table->string('number_plate')->nullable();
        $table->string('scheme')->nullable();
        $table->string('broker_name')->nullable();
        $table->text('other_remarks')->nullable();
        $table->string('rc_book')->nullable();
        $table->string('insurance_doc')->nullable();
        $table->string('owner_id_proof')->nullable();
        $table->string('pan_card')->nullable();
        $table->string('aadhar_card')->nullable();
        $table->string('noc')->nullable();
        $table->string('form_35')->nullable();
        $table->string('purchase_agreement')->nullable();
        $table->string('main_image')->nullable();
        $table->string('status')->default('available');
        $table->timestamps();
    });
    echo "Created 'old_cars' table." . PHP_EOL;
}

// 8. old_car_images
if (!$schema->hasTable('old_car_images')) {
    $schema->create('old_car_images', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('old_car_id');
        $table->string('image_url');
        $table->timestamps();
    });
    echo "Created 'old_car_images' table." . PHP_EOL;
}

// 9. extra_charges
if (!$schema->hasTable('extra_charges')) {
    $schema->create('extra_charges', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('old_car_id');
        $table->string('description')->nullable();
        $table->string('amount')->nullable();
    });
    echo "Created 'extra_charges' table." . PHP_EOL;
}

// 10. sales
if (!$schema->hasTable('sales')) {
    $schema->create('sales', function ($table) {
        $table->bigIncrements('id');
        $table->string('car_type')->nullable();
        $table->unsignedBigInteger('car_id')->nullable();
        $table->string('sale_type')->nullable();
        $table->string('total_price')->nullable();
        $table->string('sale_date')->nullable();
        $table->string('buyer_name')->nullable();
        $table->timestamps();
    });
    echo "Created 'sales' table." . PHP_EOL;
}

// 11. old_car_buyers
if (!$schema->hasTable('old_car_buyers')) {
    $schema->create('old_car_buyers', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('sale_id')->nullable();
        $table->string('name')->nullable();
        $table->string('contact')->nullable();
        $table->text('address_line_1')->nullable();
        $table->text('address_line_2')->nullable();
        $table->string('city_name')->nullable();
        $table->string('pincode')->nullable();
        $table->string('email')->nullable();
        $table->string('dob')->nullable();
        $table->string('pan_card')->nullable();
        $table->string('aadhar_card')->nullable();
        $table->string('driving_license')->nullable();
        $table->timestamps();
    });
    echo "Created 'old_car_buyers' table." . PHP_EOL;
}

// 12. full_payment_details
if (!$schema->hasTable('full_payment_details')) {
    $schema->create('full_payment_details', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('sale_id')->nullable();
        $table->double('total_amount')->default(0);
        $table->double('amount_paid')->default(0);
        $table->double('remaining_amount')->default(0);
        $table->timestamps();
    });
    echo "Created 'full_payment_details' table." . PHP_EOL;
}

// 13. payment_installments
if (!$schema->hasTable('payment_installments')) {
    $schema->create('payment_installments', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('full_payment_id')->nullable();
        $table->text('note')->nullable();
        $table->double('amount')->default(0);
        $table->dateTime('payment_date')->nullable();
        $table->string('payment_method')->nullable();
        $table->string('payment_status')->default('completed');
        $table->timestamps();
    });
    echo "Created 'payment_installments' table." . PHP_EOL;
}

// 14. dealer_full_payment_details
if (!$schema->hasTable('dealer_full_payment_details')) {
    $schema->create('dealer_full_payment_details', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('sale_id')->nullable();
        $table->double('total_amount')->default(0);
        $table->double('amount_paid')->default(0);
        $table->double('remaining_amount')->default(0);
        $table->timestamps();
    });
    echo "Created 'dealer_full_payment_details' table." . PHP_EOL;
}

// 15. dealer_payment_installments
if (!$schema->hasTable('dealer_payment_installments')) {
    $schema->create('dealer_payment_installments', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('dealer_full_payment_id')->nullable();
        $table->text('note')->nullable();
        $table->double('amount')->default(0);
        $table->dateTime('payment_date')->nullable();
        $table->string('payment_method')->nullable();
        $table->string('payment_status')->default('completed');
        $table->timestamps();
    });
    echo "Created 'dealer_payment_installments' table." . PHP_EOL;
}

// 16. loan_details
if (!$schema->hasTable('loan_details')) {
    $schema->create('loan_details', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('sale_id')->nullable();
        $table->double('loan_amount')->default(0);
        $table->integer('number_of_parts')->default(1);
        $table->double('installment_amount')->default(0);
        $table->double('interest_rate')->default(0);
        $table->timestamps();
    });
    echo "Created 'loan_details' table." . PHP_EOL;
}

// 17. installments
if (!$schema->hasTable('installments')) {
    $schema->create('installments', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('loan_id')->nullable();
        $table->integer('installment_number')->default(1);
        $table->dateTime('due_date')->nullable();
        $table->double('amount_due')->default(0);
        $table->double('amount_paid')->nullable();
        $table->dateTime('payment_date')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();
    });
    echo "Created 'installments' table." . PHP_EOL;
}

// Seed default admin user if not exists
$adminUser = User::where('email', 'admin@motobook.com')->first();
if (!$adminUser) {
    User::create([
        'email' => 'admin@motobook.com',
        'password' => 'admin123',
        'full_name' => 'MOTOBOOK Admin',
        'role' => 'admin',
        'status' => 'active',
    ]);
    echo "Seeded default admin user: admin@motobook.com / admin123" . PHP_EOL;
} else {
    echo "Admin user already exists." . PHP_EOL;
}

echo "Database initialization completed successfully!" . PHP_EOL;
