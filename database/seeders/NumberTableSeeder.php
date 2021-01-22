<?php

namespace Database\Seeders;

use App\Models\GNumber;
use App\Models\Number;
use App\Models\User;
use Illuminate\Database\Seeder;

class NumberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $seed_times = 1000;
        $start_number = 100000;
        if(Number::first()){
            $id = Number::Orderby('id','desc')->first()->id;
        }else{
            $id = $start_number;
        }

        for($i=$seed_times;$i>0;$i--){
            Number::create([
                'id' => ++$id,
                'lock' => 0,
                'used' => 0,
            ]);
        }
        \App\Models\Number::factory(1000)->create();
    }
}
