<?php

class SitterTypesSeeder extends Seeder
{

    public function run()
    {
        DB::table('sitter_types')->delete();
        SitterType::create(array(
            'name' => 'Baby Sitter',
            'color' => "#3498db"
        ));
        SitterType::create(array(
            'name' => 'Pet Sitter',
            'color' => "#e67e22"
        ));
        SitterType::create(array(
            'name' => 'House Sitter',
            'color' => "#9b59b6"
        ));
        SitterType::create(array(
            'name' => 'Plant Sitter',
            'color' => "#2ecc71"
        ));
    }

}