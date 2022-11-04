<?php
	
namespace App\Helpers\Seeders;
use App\Helpers\Formattors\ZtwenFaker;
use App\Helpers\Tools\Tools;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;

class Seeders{


	public static function run($run = false)
	{
		if(!$run){
			return false;
		}
		//CREATION DES LEVELS
		$levels = ['maternal', 'primary', 'secondary', 'superior'];
		foreach ($levels as $l) {
			Level::create(['name' => $l]);
		}

		//CREATION DES CLASSES
		$levels = Level::all();
		$school_years = SchoolYear::all();
		foreach($levels as $l){
			$classes = Tools::classes($l->name);
			if($classes){
				foreach($classes as $c){
					$c = Classe::create([
						'name' => $c,
						'slug' => $c,
						'level_id' => $l->id,
					]);
					if($c){
						$count = SchoolYear::all()->count();
                        $s_years = SchoolYear::all()->shuffle()->take(rand(5, $count));
						foreach ($s_years as $school) {
							$school->classes()->attach($c->id);
						}
					}
				}
			}


		}

		//CREATION DES MATIERES
		$levels = Level::all();
		foreach($levels as $l){
			$subjects = Tools::subjects($l->name);
			if($subjects){
				foreach($subjects as $s){
					Subject::create([
						'name' => $s,
						'level_id' => $l->id,
					]);
				}
			}
		}
		
	}



	public static function FactoryUpdater()
	{
		$secPupils = Pupil::whereLevel('secondary')->withTrashed('deleted_at')->get();
		$primPupils = Pupil::whereLevel('primary')->withTrashed('deleted_at')->get();
		$secClasses = Classe::whereLevel('secondary')->withTrashed('deleted_at')->pluck('id')->toArray();
		$primClasses = Classe::whereLevel('primary')->withTrashed('deleted_at')->pluck('id')->toArray();

		foreach ($secPupils as $secPupil) {
			$count = count($secClasses);
			shuffle($secClasses);
			$classes = array_rand($secClasses, rand(2, 6));
			foreach ($classes as $classe) {
				$insert = Classe::create([
					'year' => rand(2006, date('Y')),
					'month_start' => ZtwenFaker::month(),
					'month_end' => ZtwenFaker::month(),
					'period' => rand(1, 45). ' mois',
					'classe_id' => $classe,
					'pupil_id' => $secPupil->id
				]);
			}
		}
		foreach ($primPupils as $primPupil) {
			$count = count($primClasses);
			shuffle($primClasses);
			$classes = array_rand($primClasses, rand(2, 6));
			foreach ($classes as $classe) {
				$insert = Classe::create([
					'year' => rand(2006, date('Y')),
					'month_start' => ZtwenFaker::month(),
					'month_end' => ZtwenFaker::month(),
					'period' => rand(1, 45). ' mois',
					'classe_id' => $classe,
					'pupil_id' => $primPupil->id
				]);
			}
		}
	}

	public static function getSubjects():?array
	{
		$primarySubjects = Tools::subjects('primary');
		$secondarySubjects = Tools::subjects('secondary');

		if (auth()->check()) {
	        $creator = auth()->user()->name;
	    }
	    else{
	        $creator = null;
	    }

		$subjects = [];

		for($i = 0; $i < count($primarySubjects); $i++){
			$subjects[] = ['name' => $primarySubjects[$i], 'level' => 'primary', 'year' => ZtwenFaker::year(), 'month' => ZtwenFaker::month(), 'creator' => $creator];
		}

		for($j = 0; $j < count($secondarySubjects); $j++){
			$subjects[] = ['name' => $secondarySubjects[$j], 'level' => 'secondary', 'year' => ZtwenFaker::year(), 'month' => ZtwenFaker::month(), 'creator' => $creator];
		}

		return $subjects;

	}

	public static function getClasses():?array
	{
		if (auth()->check()) {
	        $creator = auth()->user()->name;
	    }
	    else{
	        $creator = null;
	    }
	    
		$primaryClasses = Tools::classes('primary');
		$secondaryClasses = Tools::classes('secondary');

		$subjects = [];

		for($i = 0; $i < count($primaryClasses); $i++){
			$classes[] = ['name' => $primaryClasses[$i], 'level' => 'primary', 'year' => ZtwenFaker::year(), 'month' => ZtwenFaker::month(), 'creator' => $creator];
		}

		for($j = 0; $j < count($secondaryClasses); $j++){
			$classes[] = ['name' => $secondaryClasses[$j], 'level' => 'secondary', 'year' => ZtwenFaker::year(), 'month' => ZtwenFaker::month(), 'creator' => $creator];
		}
		
		return $classes;

	}

	





}