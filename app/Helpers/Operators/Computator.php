<?php 


namespace App\Helpers\Operators;

use App\Models\Mark;
use App\Models\Pupil;
use App\Models\Subject;


class Computator{

	private $pupil;
	private $subject;
	private $trimestre;
	private $marks;
	private $modalities;

	public function __construct(int $pupil_id, int $subject, int $trimestre = 1)
	{
		$this->pupil = Pupil::find($pupil_id);
		$this->modalities = $this->pupil->classe->getModalities();
		$this->subject = Subject::find($subject);
		$this->trimestre = 'trimestre ' .$trimestre;
	}

	public function getMarks()
	{
		$pupil = $this->pupil;
		$trimestre = $this->trimestre;
		$subject = $this->subject;
		$pupilMarks = [];
		$devoirs = [];
		$interros = [];

		$epes = Mark::where('pupil_id', $pupil->id)->where('trimestre', $trimestre)->where('subject_id', $subject->id)->where('type', 'epe')->where('value', '>', 0)->get();
		$devs = Mark::where('pupil_id', $pupil->id)->where('trimestre', $trimestre)->where('subject_id', $subject->id)->where('type', 'devoir')->where('value', '>', 0)->get();

		if (count($epes) > 0) {
			foreach ($epes as $epe) {
				$interros[] = $epe->value;
			}
		}

		if (count($devs) > 0) {
			foreach ($devs as $dev) {
				$devoirs[] = $dev->value;
			}
		}

		$pupilMarks['interro'] = $interros;
		$pupilMarks['devoir'] = $devoirs;

		
		return $pupilMarks;
	}


	public function computor()
	{
		$modality = null;

		if (array_key_exists($this->subject->id, $this->modalities)) {
			$modality = $this->modalities[$this->subject->id];
		}


		$marks = $this->getMarks();
		$moyInterro = 0;
		$somDev = 0;
		$moyenne = 0;

		if ($marks !== []) {

			if ($marks['interro'] !== []) {
				if ($modality == null) {
					$som = array_sum($marks['interro']);
					$moyInterro = $som / count($marks['interro']);
				}
				else{
					$modality = $modality->value;
					$som = array_sum($this->getBestMarks($modality));
					$moyInterro = $som / count($this->getBestMarks($modality));
				}
			}

			if ($marks['devoir'] !== []) {
				$somDev = array_sum($marks['devoir']);
			}

			if ($marks['interro'] !== [] && $marks['devoir'] !== []) {
				$moyenne = ($somDev + $moyInterro) / (count($marks['devoir']) + 1);
			}
			elseif ($marks['interro'] !== [] && $marks['devoir'] == []) {
				$moyenne = $moyInterro;
			}
			elseif ($marks['interro'] == [] && $marks['devoir'] !== []) {
				$moyenne = $somDev / count($marks['devoir']);
			}

			return $moyenne;
		}

		return null;

	}

	public function getBestMarks($modality = null)
	{
		$interros = ($this->getMarks())['interro'];
		$theBestMarks = [];

		if ($interros == []) {
			return $theBestMarks;
		}

		if ($modality !== null) {
			if ($modality >= count($interros)) {
				$theBestMarks = $interros;
			}
			else{
				while (count($theBestMarks) < $modality) {
					for($i = 0; $i < count($interros); $i++){
						if ($interros[$i] == max($interros)) {
							$theBestMarks[] = $interros[$i];
							unset($interros[$i]);
						}
					}
				}	
			}
			
		}
		else{
			$theBestMarks = $interros;
		}

		return $theBestMarks;
	}



}