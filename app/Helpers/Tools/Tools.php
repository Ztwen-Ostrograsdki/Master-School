<?php
	
namespace App\Helpers\Tools;


class Tools{


	public static function subjects($level = null):?array
	{
		$allSubjects = ['Français', 'Anglais', 'Histoire-Géographie', 'LV-2', 'Philosophie', 'Sociologie', 'Histoire-Géographie', 'Poésie', 'Contes', 'EST', 'Droit', 'Mathématiques', 'Physique-Chimie-Technologie', 'Biologie', 'Informatique', 'Comptabilité', 'Sport'];

		$primarySubjects = ['Français', 'Anglais', 'Histoire-Géographie', 'Poésie', 'Contes', 'EST', 'Mathématiques', 'Informatique', 'Sport'];

		$secondarySubjects = [
			'Français', 'Anglais', 'Histoire-Géographie', 'Espagnol', 'Allemand', 'Philosophie', 'Sociologie', 'Droit', 'Mathématiques', 'Physique-Chimie-Technologie', 'Biologie', 'Informatique', 'Comptabilité', 'Economie', 'Sport',
			'Anglais Commercial', 'Résistance des matériaux', 'Mécanique Générale', 'Dessin', 'Schéma Auto', 'Mathématiques Financières',
		];

		if ($level === null) {
			return $allSubjects;
		}
		else{
			if ($level == "primary") {
				return $primarySubjects;
			}
			elseif ($level == "secondary") {
				return $secondarySubjects;
			}
			else{
				return [];
			}
		}
	}


	public static function classes($level = null):?array
	{
		$secondaryClasses = ['Sixième', 'Cinquième', 'Quatrième', 'Troisième', 'Seconde AB', 'Seconde C', 'Seconde D', 'Première AB', 'Première D', 'Première C', 'Première BTP', 'Terminale FM', 'Terminale OG', 'Terminale C'];
		$primaryClasses = ['Maternelle', 'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'];

		if ($level !== null) {
			if ($level == "primary") {
				return $primaryClasses;
			}
			elseif ($level == "secondary") {
				return $secondaryClasses;
			}
			else{
				return [];
			}
		}
		else{
			return [];
		}
	}

	public static function levels()
	{
		$levels = [
			'primary' => 'Le Primaire',
			'secondary' => 'Le Secondaire',
			'superior' => 'Le Supérieur'
		];
		return $levels;
	}

	public static function months()
	{
		$months = [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Août",
            "Septembre",
            "Octobre",
            "Novembre",
            "Décembre"

        ];
		return $months;
	}

// $roles = ['user', 'admin', 'teacher', 'parent', 'master', 'superAdmin', 'admin-teacher', 'admin-teacher-parent', 'admin-parent', 'teacher-parent', 'superAdmin-parent', 'superAdmin-teacher'];
	public static function roles()
	{
		$roles = [
			'teacher' => 'Enseignant',
			'admin' => 'Administrateur restreint',
			'superAdmin' => 'Administrateur directeur',
			'parent' => "Parents d'élèves",
			'user' => "Utilisateur",
			'master' => "Web Master",
			'admin-teacher' => "Administrateur restreint / Enseignant",
			'admin-parent' => "Administrateur restreint / Parent d'élèves",
			'teacher-parent' => "Enseignant / Parent d'élève",
			'admin-teacher-parent' => "Administrateur restreint / Enseignant / Parent d'élèves",
			'superAdmin-parent' => "Administrateur directeur / Parent d'élèves",
			'superAdmin-teacher' => "Administrateur directeur / Enseignant"
		];

		$roles = [
			'Default',
			'Fondateur',
			'Directeur',
			'Censeur',
			'Censeur Adjoint',
			'Surveillant Général',
			'Surveillant Général Adjoint',
			'Teacher',
			'Secretaire',
			'Secretaire Adjoint',
			'Agent de maintenance informatique',
			'Délégué',
			'Délégué Adjoint',
			'Assistant',
			'Agent entretien',
			"Agent d'entretien",
			"Agent de garde matinal",
			"Agent de garde nocturne",
			"Aide",
			"Autres",
		];
		return $roles;
	}

	public static function years()
	{
		$years = [2014, 2015, 2016, 2017, 2018, 2019];
		return $years;
	}

	public static function getMention($mark)
	{
		$mention = null;

		if($mark){

			if($mark >= 0 && $mark <= 20){

				if(self::numberIsBetween($mark, 0, 4.9999, false)){
					$mention = 'Très Faible';
				}
				elseif (self::numberIsBetween($mark, 5, 7.9999, false)) {
					$mention = 'Faible';
				}
				elseif (self::numberIsBetween($mark, 8, 9.9999, false)) {
					$mention = 'Insuffisante';
				}
				elseif (self::numberIsBetween($mark, 10, 11.9999, false)) {
					$mention = 'Passable';
				}
				elseif (self::numberIsBetween($mark, 12, 13.9999, false)) {
					$mention = 'Assez Bien';
				}
				elseif (self::numberIsBetween($mark, 14, 15.9999, false)) {
					$mention = 'Bien';
				}
				elseif (self::numberIsBetween($mark, 16, 18.9999, false)) {
					$mention = 'Très Bien';
				}
				elseif (self::numberIsBetween($mark, 19, 20, false)) {
					$mention = 'Excellente';
				}
			}

		}


		return $mention;

	}


	public static function numberIsBetween($mark, $start = 0, $end, $strict = false)
	{

		if($end > $start){

			if($strict){

				return ($mark > $start && $mark < $end);

			}
			else{

				return ($mark >= $start && $mark <= $end);
			}


		}
		return false;


	}

}