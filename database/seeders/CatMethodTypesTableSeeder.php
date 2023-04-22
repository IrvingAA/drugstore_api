<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatMethodTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_method_types')->delete();
        
        \DB::table('cat_method_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Efectivo',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Tarjeta',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Transferencia',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}