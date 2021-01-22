<?php

namespace Database\Seeders;

use App\Models\GNumber;
use App\Models\User;
use Illuminate\Database\Seeder;

class GNumberTableSeeder extends Seeder
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
        $start_number = 600000;
        if(GNumber::first()){
            $id = GNumber::Orderby('id','desc')->first()->id;
        }else{
            $id = $start_number;
        }

        for($i=$seed_times;$i>0;$i--){
            GNumber::create([
                'id' => ++$id,
                'lock' => 0,
                'used' => 0,
            ]);
        }
        \App\Models\GNumber::factory(1000)->create();
    }
}
