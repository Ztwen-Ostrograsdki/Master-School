<?php 


namespace App\Helpers\Operators;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\Subject;


/**
 * This trait used to computed the stat of a classe for a specific subject for specific interval 
 * @return array of @object and mixed
 */
trait Computator{

		use ModelQueryTrait;

		public function getClasseStats($intervalles_tabs, $classe_id, $type, $mark_index, $semestre, $subject, $marks_values = [], $withList = false)
		{
			$matches = [];
			$intervallesWithStatsOnly = [];
			$intervallesWithMarks = [];
			$intervallesWithMarks = [];
			$equal_sign = ' = ';
			$sup_sign = null;
			$inf_sign = null;
			$equal_value = null;
			$sup_value = null;
			$inf_value = null;
			$succeed_number = 0;
			$succeed_percentage = 0;
			$failed_number = 0;
			$failed_percentage = 0;
			$size = count($marks_values);
			$best_mark = ['mark' => 0, 'pupils' => null, 'occurence' => null];
			$weak_mark = ['mark' => 0, 'pupils' => null, 'occurence' => null];

			$classe = Classe::find($classe_id);

			if(!$classe){

				$intervallesWithStats['global_stats'] = [
					        'succeed_number' => $succeed_number,
					        'succeed_percentage' => $succeed_percentage,
					        'failed_number' => $failed_number,
					        'failed_percentage' => $failed_percentage, 
					        'presents' => intval($failed_number + $succeed_number), 
					        'absents' => 0
				];

				foreach($intervalles_tabs as $intervalle){

					$intervallesWithStatsOnly[$intval] = ['total' => 0, 'percentage' => 0, 'moy' => 0];
				}

        		$intervallesWithStats['stats'] = $intervallesWithStatsOnly;

        		return $intervallesWithStats;

			}

			foreach($intervalles_tabs as $intervalle){

				$equal = preg_match('/\=\d+/', $intervalle, $m_equal);

				if($equal){

	            	$string = $m_equal[0];

	            	$equal_value = floatval(str_replace('=', '', $string));
	            }

	            $ltoe = preg_match('/\<=\d+/', $intervalle, $m_ltoe);

	            $lt = preg_match('/\<\d+/', $intervalle, $m_lt);

	            //Get left of interval
	            if($ltoe){

	            	$string = $m_ltoe[0];

	            	$inf_sign = '<=';

	            	$inf_value = floatval(str_replace('<=', '', $string));
	            }
	            elseif($lt){

	            	$string = $m_lt[0];

	            	$inf_sign = '<';

	            	$inf_value = floatval(str_replace('<', '', $string));
	            }
	            else{

	            	$ltoe = preg_match('/\d+\>=/', $intervalle, $m_ltoe);

	            	$lt = preg_match('/\d+\>/', $intervalle, $m_lt);

	            	if($ltoe){

		            	$string = $m_ltoe[0];

		            	$inf_sign = '<=';

		            	$inf_value = floatval(str_replace('>=', '', $string));
		            }
		            elseif($lt){

		            	$string = $m_lt[0];

		            	$inf_sign = '<';

		            	$inf_value = floatval(str_replace('>', '', $string));
		            }
		            else{
		            	$string = null;

		            	$inf_sign = null;

		            	$inf_value = null;
		            }
	            }

	            $gtoe = preg_match('/\>=\d+/', $intervalle, $m_gtoe);

	            $gt = preg_match('/\>\d+/', $intervalle, $m_gt);

	            //Get left of interval

	            if($gtoe){

	            	$string = $m_gtoe[0];

	            	$sup_sign = '>=';

	            	$sup_value = floatval(str_replace('>=', '', $string));
	            }
	            elseif($gt){

	            	$string = $m_gt[0];

	            	$sup_sign = '>';

	            	$sup_value = floatval(str_replace('>', '', $string));
	            }
	            else{

	            	$gtoe = preg_match('/\d+\<=/', $intervalle, $m_gtoe);

	            	$gt = preg_match('/\d+\</', $intervalle, $m_gt);

	            	if($gtoe){

		            	$string = $m_gtoe[0];

		            	$sup_sign = '>=';

		            	$sup_value = floatval(str_replace('<=', '', $string));
		            }
		            elseif($gt){

		            	$string = $m_gt[0];

		            	$sup_sign = '>';

		            	$sup_value = floatval(str_replace('<', '', $string));
		            }

	            	else{

	            		$string = null;

		            	$sup_sign = null;

		            	$sup_value = null;

	            	}
	            }

	            $matches[$intervalle] = [
	            	'equal_sign' => $equal_sign,
	            	'sup_sign' => $sup_sign,
	            	'sup_value' => $sup_value,
	            	'inf_sign' => $inf_sign,
	            	'inf_value' => $inf_value,
	            	'equal_value' => $equal_value,
	            ];
	        }

	        $marks = [];

	        $school_year_model = $this->getSchoolYear();

	        foreach ($matches as $intervalle => $data) {

	        	$pupils = [];

	        	$pupils_names = [];

	        	$pupils_as_string = '';

	        	$sup_sign = $data['sup_sign'];

	        	$sup_value = $data['sup_value'];

	        	$average = 0;

	        	$inf_sign = $data['inf_sign'];

	        	$inf_value = $data['inf_value'];

	        	$equal_value = $data['equal_value'];

	        	if($equal_value !== null){

	        		$data = $school_year_model->marks()
	        								  ->where('classe_id', $classe_id)
	        								  ->where('type', $type)
	        								  ->where('mark_index', $mark_index)
	        								  ->where('semestre', $semestre)
	        								  ->where('subject_id', $subject)
	        								  ->where('value', $equal_value)
	        								  ->with('pupil');

	        		if(count($data->get()) >= 0){

	        			foreach ($data->get() as $pup) {

	        				$pupils[] = $pup->pupil;

	        				// $pupils_names[] = $pup->pupil->getName();

	        				// array_unique($pupils_names);

	        				// $pupils_as_string = implode(' || ', $pupils_names);
	        			}

	        			$marks = $data->get()->pluck('value')->toArray();
	        			$intervallesWithMarks[$intervalle] = ['marks' => $marks, 'pupils' => $pupils];
	        		}

	        	}
	        	if($sup_sign !== null && $inf_sign !== null){

	        		$data = $school_year_model->marks()
	        								  ->where('classe_id', $classe_id)
	        								  ->where('type', $type)
	        								  ->where('mark_index', $mark_index)
	        								  ->where('semestre', $semestre)
	        								  ->where('subject_id', $subject)
	        								  ->where('value', $sup_sign, $sup_value)
	        								  ->where('value', $inf_sign, $inf_value)
	        								  ->with('pupil');

	        		if(count($data->get()) >= 0){

	        			foreach ($data->get() as $pup) {

	        				$pupils[] = $pup->pupil;

	        				// $pupils_names[] = $pup->pupil->getName();

	        				// array_unique($pupils_names);

	        				// $pupils_as_string = implode(' || ', $pupils_names);
	        			}

	        			$marks = $data->get()->pluck('value')->toArray();

	        			$intervallesWithMarks[$intervalle] = ['marks' => $marks, 'pupils' => $pupils];
	        		}

	        	}
	        	elseif($sup_sign && $inf_sign == null){

	        		$data = $school_year_model->marks()
	        								  ->where('classe_id', $classe_id)
	        								  ->where('type', $type)
	        								  ->where('mark_index', $mark_index)
	        								  ->where('semestre', $semestre)
	        								  ->where('subject_id', $subject)
	        								  ->where('value', $sup_sign, $sup_value)
	        								  ->with('pupil');

	        		if(count($data->get()) >= 0){

	        			foreach ($data->get() as $pup) {

	        				$pupils[] = $pup->pupil;

	        				// $pupils_names[] = $pup->pupil->getName();

	        				// array_unique($pupils_names);

	        				// $pupils_as_string = implode(' || ', $pupils_names);
	        			}


	        			$marks = $data->get()->pluck('value')->toArray();

	        			$intervallesWithMarks[$intervalle] = ['marks' => $marks, 'pupils' => $pupils];
	        		}


	        	}
	        	elseif($inf_sign && $sup_sign == null){

	        		$data = $school_year_model->marks()
	        								  ->where('classe_id', $classe_id)
	        								  ->where('type', $type)
	        								  ->where('mark_index', $mark_index)
	        								  ->where('semestre', $semestre)
	        								  ->where('subject_id', $subject)
	        								  ->where('value', $inf_sign, $inf_value)
	        								  ->with('pupil');

	        		if(count($data->get()) >= 0){

	        			foreach ($data->get() as $pup) {

	        				$pupils[] = $pup->pupil;

	        				// $pupils_names[] = $pup->pupil->getName();

	        				// array_unique($pupils_names);

	        				// $pupils_as_string = implode(' || ', $pupils_names);
	        			}

	        			$marks = $data->get()->pluck('value')->toArray();

	        			$intervallesWithMarks[$intervalle] = ['marks' => $marks, 'pupils' => $pupils];
	        		}

	        	}
	        }

	        if($size > 0){

        		foreach ($intervallesWithMarks as $intval => $interval_data) {

        			$intervalle_marks = $interval_data['marks'];

        			$pupils = $interval_data['pupils'];

        			$pupils_to_get = [];

        			$pupils_names = [];

        			if(count($pupils) > 0){

        				foreach($pupils as $ppp){

        					if(!isset($pupils_to_get[$ppp->id])){

        						$pupils_names[] = $ppp->getName();

        						$pupils_to_get[$ppp->id] = $ppp;
        					}

        				}

        			}

        			$pupils = $pupils_to_get;

        			$pupils_as_string = implode(' || ', $pupils_names);

        			$liste = $pupils_as_string;

	        		$marks_size = count($intervalle_marks);

	        		if($marks_size > 0){

	        			$total = $marks_size;

	        			$percentage = floatval(number_format(($marks_size / $size) * 100, 2));

	        			$average = array_sum($intervalle_marks) / $total;
	        		}
	        		else{

	        			$total = 0;

	        			$percentage = 0;
	        		}

	        		$intervallesWithStatsOnly[$intval] = ['total' => $total, 'percentage' => $percentage, 'moy' => $average, 'pupils' => $pupils, 'liste' => $liste];
	        	}

	        	$succeed_marks = $school_year_model->marks()
	        	                                   ->where('classe_id', $classe_id)
	        	                                   ->where('type', $type)
	        	                                   ->where('mark_index', $mark_index)
	        	                                   ->where('semestre', $semestre)
	        	                                   ->where('subject_id', $subject)
	        	                                   ->where('value', '>=', 10)
	        	                                   ->pluck('value')
	        	                                   ->toArray();

	        	$failed_marks = $school_year_model->marks()
	        	                                  ->where('classe_id', $classe_id)
	        	                                  ->where('type', $type)
	        	                                  ->where('mark_index', $mark_index)
	        	                                  ->where('semestre', $semestre)
	        	                                  ->where('subject_id', $subject)
	        	                                  ->where('value', '<', 10)
	        	                                  ->pluck('value')
	        	                                  ->toArray();

	        	if(count($succeed_marks) > 0){

	        		$succeed_number = count($succeed_marks);

	        		$succeed_percentage = floatval(number_format(($succeed_number / $size) * 100, 2));
	        	}

	        	if(count($failed_marks) > 0){

	        		$failed_number = count($failed_marks);

	        		$failed_percentage = floatval(number_format(($failed_number / $size) * 100, 2));
	        	}

	        	$the_marks = $school_year_model->marks()
	        	                               ->where('classe_id', $classe_id)
	        	                               ->where('type', $type)
	        	                               ->where('mark_index', $mark_index)
	        	                               ->where('semestre', $semestre)
	        	                               ->where('subject_id', $subject)
	        	                               ->pluck('value')
	        	                               ->toArray();

	        	if($the_marks){

	        		$best_mark['mark'] = max($the_marks);

	        		$weak_mark['mark'] = min($the_marks);

	        		$best_pupils = $school_year_model->marks()
	        		                                 ->where('classe_id', $classe_id)
	        		                                 ->where('type', $type)
	        		                                 ->where('mark_index', $mark_index)
	        		                                 ->where('semestre', $semestre)
	        		                                 ->where('subject_id', $subject)
	        		                                 ->where('value', $best_mark['mark'])
	        		                                 ->with('pupil')
	        		                                 ->get();
	        		
	        		$weak_pupils = $school_year_model->marks()
	        		                                 ->where('classe_id', $classe_id)
	        		                                 ->where('type', $type)
	        		                                 ->where('mark_index', $mark_index)
	        		                                 ->where('semestre', $semestre)
	        		                                 ->where('subject_id', $subject)
	        		                                 ->where('value', $weak_mark['mark'])->with('pupil')->get();

	        		$bps = [];

	        		$wps = [];

	        		if($best_pupils->count() > 0){

	        			foreach($best_pupils as $bp){

	        				$bps[] = $bp->pupil;
	        			}
	        		}

	        		$best_mark['occurence'] = count($bps);

	        		$best_mark['pupils'] = $bps;


	        		if($weak_pupils->count() > 0){

	        			foreach($weak_pupils as $wp){

	        				$wps[] = $wp->pupil;
	        			}
	        		}

	        		$weak_mark['occurence'] = count($wps);

	        		$weak_mark['pupils'] = $wps;
	        	}
        	}
        	else{
        		foreach ($intervallesWithMarks as $intval => $intervalle_marks) {

	        		$intervallesWithStatsOnly[$intval] = ['total' => 0, 'percentage' => 0, 'pupils' => [], 'liste' => 'La liste est vide'];
	        	}
        	}

        	$effectif = count($classe->getPupils($school_year_model->school_year));

        	$presents =  $failed_number + $succeed_number;

        	$intervallesWithStats['global_stats'] = [
        		'succeed_number' => $succeed_number, 
        		'succeed_percentage' => $succeed_percentage, 
        		'failed_number' => $failed_number, 
        		'failed_percentage' => $failed_percentage, 
        		'effectif' => $effectif, 
        		'presents' => $presents, 
        		'absents' => intval($effectif - $presents),
        	];

        	$intervallesWithStats['b_w_stats'] = ['best_stats' => $best_mark,
        		'weak_stats' => $weak_mark];
        	
        	$intervallesWithStats['stats'] = $intervallesWithStatsOnly;

        	return $intervallesWithStats;

		}



}