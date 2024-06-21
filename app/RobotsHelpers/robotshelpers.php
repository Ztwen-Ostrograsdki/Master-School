<?php

use App\Models\Classe;
use App\Models\ClasseMarksStoppedForSchoolYear;
use App\Models\MarkStopped;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;

if(!function_exists('numb_formatted')){

	function numb_formatted($number){

		return $number >= 10 ? $number : '0' . $number;


	}

}


if(!function_exists('get_mark_type')){

	function get_mark_type($type){

		$types = [
			'epe' => "Interrogation",
			'devoir' => "Devoir",
			'dev' => "Devoir",
			'participation' => "Participation",
			'part' => "Participation",

		];

		return $type ? $types[$type] : 'Inconnue';


	}

}

if(!function_exists('auth_user')){

	function auth_user(){

		return auth()->user() ? auth()->user() : null;


	}

}


if(!function_exists('auth_user_id')){

	function auth_user_id(){

		return auth()->user() ? auth()->user()->id : null;


	}

}

if(!function_exists('getclasse')){

	function getclasse($id){

		$classe = Classe::find($id);

		return $classe ? $classe : null;


	}

}


if(!function_exists('getsubject')){

	function getsubject($id){

		$subject = Subject::find($id);

		return $subject ? $subject : null;


	}

}



if(!function_exists('getpupil')){

	function getpupil($id){

		$pupil = Pupil::find($id);

		return $pupil ? $pupil : null;


	}

}


if(!function_exists('getteacher')){

	function getteacher($id){

		$teacher = Teacher::find($id);

		return $teacher ? $teacher : null;


	}

}



if(!function_exists('get_school_year_model')){

	function get_school_year_model(){

		if(session()->has('school_year_selected') && session('school_year_selected')){

			$model = SchoolYear::where('school_year', $school_year)->first();

			return $model ?  $model : null;

		}

		return null;

	}

}

if(!function_exists('is_marks_stopped')){

	function is_marks_stopped($classe_id = null, $level_id, $school_year_id = null, $semestre = null){

		if(!$school_year_id){

			if(session()->has('school_year_selected') && session('school_year_selected')){

				$school_year_id = session('school_year_selected');

			}
		}

		if($school_year_id){

			if(!is_array($semestre)){

				if($classe_id){

					$stopped0 = ClasseMarksStoppedForSchoolYear::where(['school_year_id' => $school_year_id, 'classe_id' => $classe_id, 'semestre' => $semestre, 'level_id' => $level_id])->first();

				}
				else{

					$stopped0 = false;

				}

				if($semestre){

					$stopped = MarkStopped::where('school_year_id', $school_year_id)->where('level_id', $level_id)->where('semestre', $semestre)->first();

				}
				else{

					$stopped = MarkStopped::where('school_year_id', $school_year_id)->where('level_id', 'level_id')->first();

				}
			}
			else{

				$semestres = [];

				if($semestre == []){

					$school = School::first();

			        $semestres = [1, 2];

			        if($school){

			            if($school->trimestre){

			                $semestres = [1, 2, 3];
			            }
			            else{

			                $semestres = [1, 2];
			            }
			        }
				}
				else{

					$semestres = $semestre;

				}

				$matches = [];

				$matches1 = [];

				if($classe_id){

					foreach($semestres as $sem){

						$stopped = ClasseMarksStoppedForSchoolYear::where(['school_year_id' => $school_year_id, 'classe_id' => $classe_id, 'semestre' => $sem, 'level_id' => $level_id])->first();

						if($stopped){

							$matches[$sem] = $stopped;

						}

						

						$stopped1 = MarkStopped::where('school_year_id', $school_year_id)->where('level_id', $level_id)->where('semestre', $sem)->first();

						if($stopped1){

							$matches1[$sem] = $stopped1;

						}


					}

					if(count($matches) !== count($semestres)){

						return false;

					}
					elseif(count($matches1) !== count($semestres)){

						return false;

					}
					elseif(count($matches1) == count($semestres) && count($matches) == count($semestres)){

						foreach($matches1 as $stpd){

							if(!$stpd || $stpd && !$stpd->stopped){

								return false;

							}

						}

						return true;

					}

				}
				else{

					$stopped0 = false;

				}




			}
		}

		return ($stopped && $stopped->stopped) && $stopped0;

	}

}








