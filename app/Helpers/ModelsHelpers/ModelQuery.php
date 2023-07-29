<?php
namespace App\Helpers\ModelsHelpers;

use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;

class ModelQuery{

    public $classe;
    public $classe_name;
    public $pupils = [];
    public $school_year;


    public function __construct($classMapping, $model_id, $school_year)
    {
        if(!$school_year){
            if(session()->has('school_year_selected') && session('school_year_selected')){
                $school_year = session('school_year_selected');
            }
            else{
                $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
            }
        }
        
        $this->classMapping = $classMapping;
        $this->school_year = $school_year;
        $this->model_id = $model_id;
        $this->school_year = $school_year;
    }


    // get_class($model);
    // $class = str_replace('-', '\\', $classMapping);
    // $model = new $class;
    // $this->classMapping = $model;
    // $this->modelTable = get_class($model);

    public function setClasse($column = 'slug', $columnValue)
    {
        $allClasses = Classe::where($column, urldecode($columnValue))->get();
        $school_year = $this->school_year;
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        
        $classe = $school_year_model->classes()->where($column, urldecode($columnValue))->first();

        if($allClasses->count() > 0){
            $this->classe_name = $allClasses->first()->name;
            if($classe){
                $this->classe = $classe;
            }
        }

        return $this;
    }


    public function getClasse()
    {
        return $this->classe;
    }







    /**
     * Get the value of pupils
     */ 
    public function getPupilsOfThisSChoolYear()
    {
        return $this->pupils;
    }

    /**
     * Set the value of pupils
     *
     * @return  self
     */ 
    public function setPupils($column = 'slug', $columnValue, $school_year = null)
    {
        $allClasses = Classe::where($column, urldecode($columnValue))->get();           
        if(!$school_year){
            $school_year = $this->school_year;
        }
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();

        $this->classe_name = $allClasses->first()->name;
        if($this->classe){
            $pupils = [];
            $all_pupils = $this->classe->pupils;
            foreach($all_pupils as $p){
                if($p->school_years){
                    $pupil_of_selected_school_year = $p->school_years()->where('school_year', $school_year)->first();
                    if($pupil_of_selected_school_year){
                        $pupils[] = $p;
                    }
                }
            }
        }

        $this->pupils = $pupils;

        return $this;
    }
}