<?php

namespace App\Http\Livewire;

use App\Events\InitiateMarksStoppingEvent;
use App\Helpers\AdminTraits\AdminTrait;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TeacherListingByClasse extends Component
{
    use ModelQueryTrait;

    use AdminTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData', 
        'GlobalDataUpdated' => 'reloadData',
        'MarksStoppingDispatchedLiveEvent' => 'reloadData',
    ];

    public $classe_id = null;

    public $counter = 0;

    public $occurence = 0;

    public $baseRoute = 'teacher_listing';

    public $search = '';

    public $semestre_type = 'Semestre';

    public $selected_classe;


    public function mount($slug)
    {
        if($slug){

            $this->slug = $slug;
        }
        else{
            return abort(404);
        }

    }


    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $school = School::first();

        $semestres = [1, 2];



        if($school){

            if($school->trimestre){

                $this->semestre_type = 'Trimestre';

                $semestres = [1, 2, 3];
            }
            else{

                $semestres = [1, 2];
            }
        }

        $teachers = [];

        $classe = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();

        if($classe){

            $this->classe_id = $classe->id;

            $teachers = $classe->getClasseCurrentTeachers();

        }

        return view('livewire.teacher-listing-by-classe', compact('classe', 'school_year_model', 'teachers', 'semestres'));
    }

    public function updatedSelectedClasse($classe_id)
    {
        $classe = Classe::find($classe_id);

        if($classe && $classe->id !== $this->classe_id){

            $this->selected_classe = $classe;

            redirect()->route('classe_teachers', ['slug' => $classe->getSlug()]);

        }
        else{

            $this->selected_classe = null;

        }

    }

    public function closeSemestre($semestre)
    {

        $school_year_model = $this->getSchoolYear();

        $classe = Classe::find($this->classe_id);

        if($classe){

            $verify_semestre_marks_status = $classe->getTheClasseSemestreMarksStatus($semestre);

            if($verify_semestre_marks_status && $verify_semestre_marks_status['status']){

                $level = $classe->level;

                InitiateMarksStoppingEvent::dispatch($classe, $level, $school_year_model, $semestre, null, []);

            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "DES MATIERES AVEC IRREGULARITES DE NOTES", 'message' => "Des matières avec des irrégularités de notes ont étés détectées dans cette classe. Vous ne pouvez pas cloturer le semestre dans cette classe si ces irrégularités de notes n'ont pas été corrigées!", 'type' => 'warning']);

            }
        }
    }


    public function closeSchoolYear()
    {

        $school_year_model = $this->getSchoolYear();

        $classe = Classe::find($this->classe_id);

        if($classe){

            $level = $classe->level;

            $semestre = session('semestre_selected');

            InitiateMarksStoppingEvent::dispatch($classe, $level, $school_year_model, null, null, []);

        }

    }


    public function reloadData()
    {
        $this->counter = rand(0, 14); 
    }


    public function manageClasseTeachers()
    {
        $this->emit('ManageClasseTeachers', $this->classe_id);
    }

    public function destroyClasseSecuritiesKeys($target = null)
    {

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);



        $keys = [];

        if($classe){

            if(!$target){

                $keys = $classe->securities()->pluck('classes_securities.id')->toArray();

            }
            else{

                if($target == 'teachers'){

                    $keys = $classe->securities()
                                   ->whereNotNull('classes_securities.teacher_id')
                                   ->pluck('classes_securities.id')
                                   ->toArray();

                }
                elseif($target == 'marks'){

                    $keys = $classe->securities()
                                   ->where('classes_securities.locked_marks', true)
                                   ->orWhere('classes_securities.locked_marks_updating', true)
                                   ->pluck('classes_securities.id')
                                   ->toArray();

                }

            }

        }
        if($keys){

            $this->__destroyClasseSecuritiesKeyExpired($keys);

        }

    }



    public function notifyKeysDeleted($occurence)
    {
        if($occurence){

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR REUSSIE', 'message' => "$occurence clés ont été supprimé avec succès!", 'type' => 'success']);

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'RECHERCHE TERMINEE', 'message' => "Aucune clé expirée n'a été trouvé!", 'type' => 'info']);

        }

    }

    public function generateClasseSecurity($teacher_id = null, $secure_column, $action = 'true')
    {

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        if($teacher_id){

            $teachers = $school_year_model->findTeachers([$teacher_id]);
        }
        else{

            $teachers = $classe->getClasseCurrentTeachers();

        }


        if(count($teachers) > 0){

            if($secure_column){

                DB::transaction(function($e) use($teachers, $secure_column, $action, $classe){

                    foreach($teachers as $teacher){

                        if($action == 'true'){

                            $done = $classe->generateClassesSecurity($secure_column, $teacher->id, $teacher->speciality()->id, 48, true);
                        }
                        else{

                            $done = $classe->destroyClasseSecurity($secure_column, $teacher->id);

                        }

                        if($done){

                            $this->occurence = $this->occurence + 1;

                        }

                    }

                });

                DB::afterCommit(function(){

                    $occurence = $this->occurence;

                    $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => " $occurence requêtes ont été effectuées!", 'type' => 'success']);

                    $this->emit('userDataEdited');

                    $this->reset('occurence');


                });

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " La requête est ambigüe, veuillez spécifier l'action à éxécuter!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " La requête est ambigüe, veuillez spécifier l'action à éxécuter!", 'type' => 'error']);

        }

    }



}
