<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_statuses')->delete();
        
        \DB::table('cat_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Revisión',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Pendiente',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Enviado',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}