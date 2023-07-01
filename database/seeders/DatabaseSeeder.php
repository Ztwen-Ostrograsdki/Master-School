<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Mark;
use App\Models\Role;
use App\Models\User;
use App\Models\Pupil;
use App\Models\Classe;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Str;
use App\Helpers\Tools\Tools;
use Illuminate\Database\Seeder;
use App\Helpers\Seeders\Seeders;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        

        // for ($i=0; $i < 10; $i++) { 
        //     $faker = Factory::create();
        //     $role = Role::all()->pluck('id')->shuffle()->first();
        //     User::create([
        //         'pseudo' => $faker->name(),
        //         'email' => $faker->unique()->safeEmail(),
        //         'email_verified_at' => now(),
        //         'role_id' => $role,
        //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //         'remember_token' => Str::random(10),
        //     ]);
        // }

        // $roles = config('app.local_roles');

        // foreach ($roles as $role) {
        //     Role::create([
        //       'name' => $role
        //   ]);



            
        // }


      // $school_years = SchoolYear::all();
      // if(count($school_years) < 1){
      //     $date = intval(date('Y'));
      //     for ($i=2018; $i <= $date; $i++) { 
      //         $y = $i . ' - ' . ($i+1);
      //         SchoolYear::create([
      //             'school_year' => $y,
      //         ]);
      //     }

      //     $school_years = SchoolYear::all();

      //     School::create([
      //         'name' => 'Ecole Ztwen'
      //     ]);
      // }
       


      //   $faker = Factory::create();
      //   Seeders::run(true);

      //   $roles = Tools::roles();
      //   foreach ($roles as $role) {
      //       Role::create([
      //           'name' => $role,
      //       ]);
      //   }

      //   for ($i=0; $i < 10; $i++) { 
      //       $role = Role::all()->pluck('id')->shuffle()->first();
      //       User::create([
      //           'pseudo' => $faker->name(),
      //           'email' => $faker->unique()->safeEmail(),
      //           'email_verified_at' => now(),
      //           'role_id' => $role,
      //           'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
      //           'remember_token' => Str::random(10),
      //       ]);
      //   }
        
        // $classes = Classe::all();

        // foreach ($classes as $classe) {
        //     $size = rand(15, 20);
        //     $sexe = ['male', 'female'];
        //     for ($i=0; $i < $size; $i++) { 
        //         $p = Pupil::create([
        //             'firstName' => $faker->firstName(),
        //             'lastName' => $faker->lastName(),
        //             'classe_id' => $classe->id,
        //             'sexe' => $sexe[rand(0, 1)],
        //             'birth_day' => $faker->date(),
        //             'nationality' => 'Béninoise',
        //             'level_id' => $classe->level_id,
        //         ]);
        //         if($p){
        //             if($p){
        //                 $count = SchoolYear::all()->count();
        //                 $s_years = SchoolYear::all()->shuffle()->take(rand(5, $count));
        //                 foreach ($s_years as $s) {
        //                     $s->pupils()->attach($p->id);
        //                 }
        //             }
        //         }
        //     }
        // }


        // $school_years = SchoolYear::all();

        // foreach($school_years as $school_year){

        //     $classes = $school_year->classes()->where('classes.level_id', 3)->get();

        //     foreach($classes as $classe){
        //         $pupils = $classe->getClassePupils($school_year->school_year);
        //         $subjects = $classe->subjects;
        //         if($subjects && count($subjects) > 0){
        //             foreach($subjects as $subject){
        //                 if($pupils && count($pupils) > 0){
        //                     foreach($pupils as $pupil){
        //                         //Interro
        //                         for($i = 0; $i < rand(2, 6); $i++){
        //                             $value = rand(7, 20);
        //                             $epe = Mark::create(
        //                                 [
        //                                     'value' => $value, 
        //                                     'pupil_id' => $pupil->id, 
        //                                     'subject_id' => $subject->id, 
        //                                     'classe_id' => $classe->id, 
        //                                     'semestre' => 1, 
        //                                     'type' => 'epe', 
        //                                     'month' => 'septembre', 
        //                                     'level_id' => $pupil->level_id, 
        //                                 ]
        //                             );

        //                             if($epe){
        //                                 $school_year->marks()->attach($epe->id);
        //                             }
        //                         }

        //                         //Devoir
        //                         for($i = 0; $i <= rand(1, 2); $i++){
        //                             $value = rand(4, 19);
        //                             $dev = Mark::create(
        //                                 [
        //                                     'value' => $value, 
        //                                     'pupil_id' => $pupil->id, 
        //                                     'subject_id' => $subject->id, 
        //                                     'classe_id' => $classe->id, 
        //                                     'semestre' => 1, 
        //                                     'type' => 'devoir', 
        //                                     'month' => 'octobre', 
        //                                     'level_id' => $pupil->level_id, 
        //                                 ]
        //                             );

        //                             if($dev){
        //                                 $school_year->marks()->attach($dev->id);
        //                             }
        //                         }
        //                     }
        //                 }
        
        //             }

        //         }

                

        //     }




        // }
    }

}
