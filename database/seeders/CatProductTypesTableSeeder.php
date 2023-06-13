<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatProductTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_product_types')->delete();
        
        \DB::table('cat_product_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Abarrotes',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Farmacia',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}