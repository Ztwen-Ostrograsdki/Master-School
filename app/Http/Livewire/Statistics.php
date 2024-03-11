<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Helpers\Tools\Tools;
use App\Models\Filial;
use Livewire\Component;

class Statistics extends Component
{

    use ModelQueryTrait;

    public $classe_id;

    public $classe_group_id = 'all';

    public $promotion = 'all';

    public $teacher_id;

    public $showList = true|false;

    public $school_year_model;

    public $semestre_type = 'Semestre';

    public $school_year;

    public $subject_selected;

    public $subject = 'la matière';

    public $semestre_selected = 1;

    public $type = 'devoir';

    public $mark_index = 1;

    public $counter = 0;

    public $subject_id;

    public $filial_id = 'all';

    public $intervalles = 'N<7;7<=N<9;9<=N<10;10<=N<12;N>=12';

    public $size = 0;

    public $stats = [];



    public $types = [
        'devoir' => 'Devoirs',
        'moyenne' => "Moyennes",
        'cap' => "CAP",
        'dt' => "DT",
        'bac' => "BAC"
    ];


    public function render()
    {
        $semestres = $this->getSemestres();

        $school_year_model = $this->getSchoolYear();

        $subjects = $school_year_model->subjects;

        $filials = Filial::orderBy('name', 'asc')->get();

        $classe_groups = [];

        // $classe_groups = $school_year_model->classe_groups;

        $promotions = Tools::getClassesPositionAsPromotions();

        $maxLenght = 4;

        $this->semestre_selected = session('semestre_selected');

        if(count($semestres) == 3){

            $this->semestre_type = 'Trimestre';

        }
        return view('livewire.statistics', compact('semestres', 'school_year_model', 'maxLenght', 'classe_groups', 'subjects', 'filials', 'promotions'));
    }







}
