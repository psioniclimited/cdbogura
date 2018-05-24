<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

// Go to DatabaseSeeder.php and uncomment the correspoding line
// run in command line - php artisan db:seed

// $factory->define(App\Modules\User\Models\User::class, function (Faker\Generator $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->safeEmail,
//        'password' => bcrypt('qwerasdf'),
//        'remember_token' => str_random(10),
//        'territory_id' => 1
//    ];
// });

// $factory->define(App\Modules\CableManagement\Models\Territory::class, function (Faker\Generator $faker) {
//    return [
//        'name' => 'Uttara',
//        'address' => 'Uttara'
//    ];
// });

// $factory->define(App\Modules\CableManagement\Models\Sector::class, function (Faker\Generator $faker) {
//    return [
//        'sector' => $faker->randomNumber(2),
//        'territory_id' => 1
//    ];
// });

// $factory->define(App\Modules\CableManagement\Models\Road::class, function (Faker\Generator $faker) {
//    return [
//        'road' => $faker->randomNumber(3),
//        'sectors_id' => rand(1,12)
//    ];
// });

// $factory->define(App\Modules\CableManagement\Models\House::class, function (Faker\Generator $faker) {
//    return [
//        'house' => $faker->bothify('??-##'),
//        'roads_id' => rand(1,40)
//    ];
// });

// $factory->define(App\Modules\CableManagement\Models\Customer::class, function (Faker\Generator $faker) {
//    return [
//        'customer_code' => $faker->bothify('DK###??'),
//        'name' => $faker->name,
//        'phone' => $faker->e164PhoneNumber,
//        'active'=> 1,
//        'subscription_types_id'=>1,
//        'houses_id' => rand(1,120),
//        'sectors_id' => rand(1,12),
//        'territory_id' => 1,
//        'last_paid' => '2016-09-01'
//    ];
// });