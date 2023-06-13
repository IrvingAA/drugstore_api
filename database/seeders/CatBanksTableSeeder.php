<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatBanksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_banks')->delete();
        
        \DB::table('cat_banks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'BBVA',
                'is_active' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Banamex',
                'is_active' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}