<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\student;
use Illuminate\Support\Facades\File;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  student::factory(10)->create();
        
        $json=File::get(path:'database/json/db.json');
        $students=collect(json_decode($json));

            $students->each(function($student){
                student::create([
                    'email'=>$student->email,
                    'city'=>$student->city,
                    'age'=>$student->age
        ]);
            });


        // student::create([
            
        //     'email'=>'abc@gmail.com',
        //     'city'=>'bharuch',
        //     'age'=>18
        // ]);
    }
}
