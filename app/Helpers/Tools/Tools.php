<?php
	
namespace App\Helpers\Tools;

use App\Models\Classe;
use App\Models\Filial;


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

	public static function numberIsBetweenLFGT($mark, $start = 0, $end)
	{

		if($end > $start){

			return ($mark >= $start && $mark < $end);

		}
		return false;


	}


	public static function getFilials()
	{

		$filials = [
            'FC' => ['name' => 'FC', 'description' => 'Froid et Climatisation', 'option' => 'Industrielle'], 
            'MA' => ['name' => 'MA', 'description' => 'Mécanique et Auto', 'option' => 'Industrielle'],
            'OBB' => ['name' => 'OBB', 'description' => 'OBB', 'option' => 'Industrielle'],
            'OG' => ['name' => 'OG', 'description' => 'Opérateur Géomètre', 'option' => 'Industrielle'],
            'FM' => ['name' => 'FM', 'description' => 'Fabrication Mécanique', 'option' => 'Industrielle'],
            'BTP' => ['name' => 'BTP', 'description' => 'Batiment et Travaux Publics', 'option' => 'Industrielle'],
            'F1' => ['name' => 'F1', 'description' => 'Mécanique Générale', 'option' => 'Technique'],
            'F2' => ['name' => 'F2', 'description' => 'Electronique', 'option' => 'Technique'],
            'F3' => ['name' => 'F3', 'description' => 'Mécanique Générale', 'option' => 'Technique'],
            'F4' => ['name' => 'F4', 'description' => 'Génie Civil', 'option' => 'Technique'],
            'IMI' => ['name' => 'IMI', 'description' => 'Installation et Maintenance Industrielle', 'option' => 'Informatique'],
            'HR' => ['name' => 'HR', 'description' => 'Hotellerie et Restauration', 'option' => null],
        ];

        return $filials;

	}


	public static function getClassesPositionAsPromotions($level = 'secondary')
	{

        if ($level === "secondary") {

        	$positions = [

        		'1AI (1ère Année)' => 1,
        		'2AI (2ème Année)' => 2,
        		'3AI (3ème Année)' => 3,
        		'Sixième' => 4,
        		'Cinquième' => 5,
        		'Quatrième' => 6,
        		'Troisième' => 7,
        		'Seconde' => 8,
        		'Première' => 9,
        		'Terminale' => 10,

        	];
        }
        elseif($this->level == 'primary'){

            $positions = [

        		'Maternelle 1' => 1,
        		'Maternelle 2' => 2,
        		'CI' => 3,
        		'CP' => 4,
        		'CE1' => 5,
        		'CE2' => 6,
        		'CM1' => 7,
        		'CM2' => 8,
        	];
        }

        return $positions;


	}


	public static function updateClassesLogs()
	{

		$filials = Filial::all();

        $classes = Classe::all();


        foreach($filials as $filial){

            $name = $filial->name;

            foreach($classes as $cl){

                if (preg_match_all('/'.$name.'/', $cl->name)) {

                    $cl->update(['filial_id' => $filial->id]);

                    $promotion = $cl->classe_group;

                    if($promotion && $promotion->filial_id == null){

                        $promotion->update(['filial' => $name, 'filial_id' => $filial->id]);
                    }

                }

            }

            

        }


	}

}