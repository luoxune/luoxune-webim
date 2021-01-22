<?php

namespace Database\Factories;

use App\Models\GNumber;
use App\Models\Number;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GNumber::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $model = Number::all()->last();
        return [
            'id' => ++$model->id,
            'lock' => 0,
            'used' => 0,
        ];
    }
}
