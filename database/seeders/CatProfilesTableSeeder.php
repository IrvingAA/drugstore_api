<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_profiles')->delete();
        
        \DB::table('cat_profiles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Administrador',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Vendedor',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Super Admin',
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}