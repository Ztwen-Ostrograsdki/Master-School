<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClasseGroup;
use App\Models\Level;
use App\Models\QotHour;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuotaManager extends Component
{
    protected $listeners = ['ManageQuotaLiveEvent' => 'openModal'];

    public $classe_id;
    public $classe_group_id;
    public $school_year_id;
    public $subject_id;
    public $section = 'classe';
    public $quotaModel;
    public $quota = 2;


    use ModelQueryTrait;


    public function render()
    {
        $school_year_model = $this->getSchoolYear($this->school_year_id);

        $classe_groups = ClasseGroup::all();

        $school_years = SchoolYear::all();

        $classes = $school_year_model->classes;

        $level_id = Level::where('name', 'secondary')->first()->id;

        $subject = null;

        $classe_group_selected = null;

        $classe_selected = null;

        $subject_selected = null;


        $subjects = $school_year_model->subjects()->where('subjects.level_id', $level_id)->get();

        if($this->section == 'classe' && $this->classe_id){

            $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();

            if($classe){

                $classe_selected = $classe;

                $subjects = $classe->subjects;
            }

        }
        elseif($this->section == 'classe_group'){

            $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id)->first();

            if($classe_group){

                $classe_group_selected = $classe_group;
                
                $subjects = $classe_group->subjects;
            }
        }


        if($this->subject_id){

            $subject_selected = Subject::find($this->subject_id);

        }

        return view('livewire.quota-manager', compact('school_year_model', 'classes', 'classe_groups', 'subjects', 'school_years', 'subject', 'classe_group_selected', 'classe_selected', 'subject_selected'));
    }


    public function submitQuota()
    {
        if($this->section == 'classe'){
            $this->validate(['classe_id' => 'required', 'quota' => 'required|int', 'school_year_id' => 'required|int', 'subject_id' => 'required|int']);
        }
        elseif($this->section == 'classe_group'){
            $this->validate(['classe_group_id' => 'required', 'quota' => 'required|int', 'school_year_id' => 'required|int', 'subject_id' => 'required|int']);
        }

        $school_year_model = $this->getSchoolYear($this->school_year_id);


        DB::transaction(function($e) use($school_year_model){

            if($this->quotaModel){
                //UPDATING
                if($this->section == 'classe'){

                    $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();

                    $hasAlreadyQuota = $classe->hasQuota($this->subject_id, $this->school_year_id);

                    if($this->quotaModel->school_year_id == $school_year_model->id){

                        $quotaModel = $this->quotaModel->update([
                            'classe_id' => $this->classe_id,
                            'subject_id' => $this->subject_id,
                            'quota' => $this->quota,
                        ]);

                        if($quotaModel){

                            $this->dispatchBrowserEvent('hide-form');

                            $this->resetErrorBag();

                            $this->emit('QuotaTableUpdated');

                            $this->reset('quota', 'subject_id', 'school_year_id', 'classe_id', 'classe_group_id');

                            $this->dispatchBrowserEvent('Toast', ['title' => 'OPERATION TERMINEE', 'message' => "Le quota horaire a été inséré avec succès!", 'type' => 'success']);

                        }
                        else{

                            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);
                        }

                    }
                    else{

                        $this->dispatchBrowserEvent('Toast', ['title' => 'QUOTA DEJA DEFINI', 'message' => "Le quota est déja défini!", 'type' => 'info']);

                    }

                }
                elseif($this->section == 'classe_group'){

                    $classe_group = ClasseGroup::find($this->classe_group_id);

                    if($classe_group){

                        $classes = $classe_group->classes;

                        if(count($classes) > 0){

                            foreach($classes as $classe){
                                
                                $hasAlreadyQuota = $classe->hasQuota($this->subject_id, $this->school_year_id);

                                if($hasAlreadyQuota){

                                    if($hasAlreadyQuota->delete()){

                                        $quotaModel = QotHour::create([
                                            'classe_id' => $classe->id,
                                            'subject_id' => $this->subject_id,
                                            'school_year_id' => $this->school_year_id,
                                            'quota' => $this->quota,
                                        ]);

                                        if(!$quotaModel){

                                            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);

                                            return false;

                                        }

                                    }
                                    else{
                                        $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);

                                            return false;

                                    }

                                    

                                }
                                else{

                                    $quotaModel = QotHour::create([
                                        'classe_id' => $this->classe_id,
                                        'subject_id' => $this->subject_id,
                                        'school_year_id' => $this->school_year_id,
                                        'quota' => $this->quota,
                                    ]);

                                    if(!$quotaModel){

                                        $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);

                                        return false;

                                    }

                                }

                            }


                        }


                    }

                    DB::afterCommit(function(){
                    
                        $this->dispatchBrowserEvent('hide-form');

                        $this->emit('QuotaTableUpdated');

                        $this->resetErrorBag();

                        $this->reset('quota', 'subject_id', 'school_year_id', 'classe_id', 'classe_group_id');

                        $this->dispatchBrowserEvent('Toast', ['title' => 'OPERATION TERMINEE', 'message' => "Le quota horaire a été inséré avec succès!", 'type' => 'success']);

                    });

                }




            }
            else{
                //CREATING
                if($this->section == 'classe'){

                    $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();

                    $hasAlreadyQuota = $classe->hasQuota($this->subject_id, $this->school_year_id);

                    if(!$hasAlreadyQuota){

                        $quotaModel = QotHour::create([
                            'classe_id' => $this->classe_id,
                            'subject_id' => $this->subject_id,
                            'school_year_id' => $this->school_year_id,
                            'quota' => $this->quota,
                        ]);

                        if($quotaModel){

                            $this->dispatchBrowserEvent('hide-form');

                            $this->resetErrorBag();

                            $this->emit('QuotaTableUpdated');

                            $this->reset('quota', 'subject_id', 'school_year_id', 'classe_id', 'classe_group_id');

                            $this->dispatchBrowserEvent('Toast', ['title' => 'OPERATION TERMINEE', 'message' => "Le quota horaire a été inséré avec succès!", 'type' => 'success']);

                        }
                        else{

                            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);
                        }

                    }
                    else{

                        $this->dispatchBrowserEvent('Toast', ['title' => 'QUOTA DEJA DEFINI', 'message' => "Le quota est déja défini!", 'type' => 'info']);

                    }

                }
                elseif($this->section == 'classe_group'){

                    $classe_group = ClasseGroup::find($this->classe_group_id);

                    if($classe_group){

                        $classes = $classe_group->classes;

                        if(count($classes) > 0){

                            foreach($classes as $classe){
                                
                                $hasAlreadyQuota = $classe->hasQuota($this->subject_id, $this->school_year_id);

                                if(!$hasAlreadyQuota){

                                    $quotaModel = QotHour::create([
                                        'classe_id' => $classe->id,
                                        'subject_id' => $this->subject_id,
                                        'school_year_id' => $this->school_year_id,
                                        'quota' => $this->quota,
                                    ]);

                                    if(!$quotaModel){

                                        $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);

                                        return false;

                                    }

                                }

                            }

                        }

                    }

                    DB::afterCommit(function(){

                        $this->emit('QuotaTableUpdated');
                    
                        $this->dispatchBrowserEvent('hide-form');

                        $this->resetErrorBag();

                        $this->reset('quota', 'subject_id', 'school_year_id', 'classe_id', 'classe_group_id');

                        $this->dispatchBrowserEvent('Toast', ['title' => 'OPERATION TERMINEE', 'message' => "Le quota horaire a été inséré avec succès!", 'type' => 'success']);

                    });

                }

            }

        });

    }


    public function openModal($quotaModel_id = null, $subject_id = null, $classe_id = null, $classe_group_id = null)
    {

        
        if($quotaModel_id){

            $quotaModel = QotHour::find($quotaModel_id);

            if($quotaModel){

                $this->quotaModel = $quotaModel;

                $this->quota = $quotaModel->quota;

                $this->classe_id = $quotaModel->classe_id;

                $this->school_year_id = $quotaModel->school_year_id;

                $this->subject_id = $quotaModel->subject_id;
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'REQUETE INCONNUE', 'message' => "Le quota est introuvable!", 'type' => 'error']);

                return false;
            }
        }
        else{

            $school_year_model = $this->getSchoolYear();

            $this->school_year_id = $school_year_model->id;

            if($subject_id){

                $this->subject_id = $subject_id;

            }

            if($classe_id){

                $this->classe_id = $classe_id;

                $this->section = 'classe';

            }
            elseif($classe_group_id){

                $this->classe_group_id = $classe_group_id;

                $this->section = 'classe_group';

            }

        }

        $this->dispatchBrowserEvent('modal-quotaManager');
    }


    public function updatedClasseId($classe_id)
    {
        $this->classe_id = $classe_id;
    }


    public function updatedClasseGroupId($classe_group_id)
    {
        $this->classe_group_id = $classe_group_id;
    }


    public function updatedSection($section)
    {
        $this->section = $section;
    }


    public function updatedSubjectId($subject_id)
    {
        $this->subject_id = $subject_id;
    }

}
