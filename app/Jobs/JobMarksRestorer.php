<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobMarksRestorer implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $school_year_model;

    public $pupil;

    public $pupil_id;

    public $data = [];

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, SchoolYear $school_year_model, $data)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->data = $data;

        if($this->data && isset($this->data['pupil_id'])){

            if($this->data['pupil_id']){

                $pupil_id = $this->data['pupil_id'];

                $pupil = Pupil::find($pupil_id);

                if($pupil){

                    $this->pupil = $pupil;

                    $this->pupil_id = $pupil_id;

                }

            }

        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->classe && !$this->pupil_id){

            $this->doJobForClasse();

        }
        elseif($this->classe && $this->pupil_id){

            $this->doJobForPupil();

        }
    }


    public function doJobForClasse()
    {
        $data = $this->data;

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        DB::transaction(function($e) use($classe, $data, $school_year_model) {

            if($data != []){

                $subject = $data['subject'];

                $semestre = $data['semestre'];

                $type = $data['type'];

                $start = $data['start'];

                $end = $data['end'];

                if($subject && $semestre && $type){

                    if($start && $end){

                        if($subject == 'all' && $semestre == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                            ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                            ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        elseif($subject == 'all' && $semestre == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.type', $type)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                        ->where('related_marks.type', $type)
                                        ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($subject == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.semestre', $semestre)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                        }
                        elseif($semestre == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.subject_id', $subject->id)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                        ->where('related_marks.subject_id', $subject->id)
                                        ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        else{
                            if($semestre == 'all'){

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($type == 'all'){

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($subject == 'all'){

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });



                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            else{

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });



                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                        }
                    }
                    else{

                        if($subject == 'all' && $semestre == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                            ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                            ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        elseif($subject == 'all' && $semestre == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.type', $type)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                        ->where('related_marks.type', $type)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($subject == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.semestre', $semestre)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                        ->where('related_marks.semestre', $semestre)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($semestre == 'all' && $type == 'all'){

                            $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.subject_id', $subject->id)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                        ->where('related_marks.subject_id', $subject->id)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        else{
                            if($semestre == 'all'){
                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($type == 'all'){

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($subject == 'all'){

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            else{

                                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }

                        }

                    }

                }
            }
            else{

                $classe->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->each(function($mark){

                    if($mark->pupil && $mark->classe && $mark->subject){

                        $mark->restore();
                    }

                });

                $classe->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->each(function($r_m){

                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                        $r_m->restore();
                    }

                });

            }

        });
    }


    public function doJobForPupil()
    {
        $data = $this->data;

        $classe = $this->classe;

        $pupil = $this->pupil;

        $school_year_model = $this->school_year_model;

        DB::transaction(function($e) use($classe, $data, $school_year_model, $pupil) {

            if($data != []){

                $subject = $data['subject'];

                $semestre = $data['semestre'];

                $type = $data['type'];

                $start = $data['start'];

                $end = $data['end'];

                if($subject && $semestre && $type){

                    if($start && $end){

                        if($subject == 'all' && $semestre == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                            ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                            ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        elseif($subject == 'all' && $semestre == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.type', $type)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                        ->where('related_marks.type', $type)
                                        ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($subject == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.semestre', $semestre)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                        }
                        elseif($semestre == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.subject_id', $subject->id)
                                        ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                        ->where('related_marks.subject_id', $subject->id)
                                        ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        else{
                            if($semestre == 'all'){

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($type == 'all'){

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($subject == 'all'){

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });



                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            else{

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->whereBetween('marks.created_at', [$start, $end])->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });



                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->whereBetween('related_marks.created_at', [$start, $end])->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                        }
                    }
                    else{

                        if($subject == 'all' && $semestre == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                            ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                            ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        elseif($subject == 'all' && $semestre == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.type', $type)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                        ->where('related_marks.type', $type)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($subject == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.semestre', $semestre)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                        ->where('related_marks.semestre', $semestre)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });

                        }
                        elseif($semestre == 'all' && $type == 'all'){

                            $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                        ->where('marks.subject_id', $subject->id)
                                        ->each(function($mark){

                                if($mark->pupil && $mark->classe && $mark->subject){

                                    $mark->restore();
                                }

                            });

                            $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                        ->where('related_marks.subject_id', $subject->id)
                                        ->each(function($r_m){

                                if($r_m->classe && $r_m->pupil && $r_m->subject){

                                    $r_m->restore();
                                }

                            });


                        }
                        else{
                            if($semestre == 'all'){
                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($type == 'all'){

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            elseif($subject == 'all'){

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }
                            else{

                                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)
                                                ->where('marks.semestre', $semestre)
                                                ->where('marks.type', $type)
                                                ->where('marks.subject_id', $subject->id)
                                                ->each(function($mark){

                                    if($mark->pupil && $mark->classe && $mark->subject){

                                        $mark->restore();
                                    }

                                });

                                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)
                                                ->where('related_marks.semestre', $semestre)
                                                ->where('related_marks.type', $type)
                                                ->where('related_marks.subject_id', $subject->id)
                                                ->each(function($r_m){

                                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                                        $r_m->restore();
                                    }

                                });

                            }

                        }

                    }

                }
            }
            else{

                $pupils->marks()->withTrashed('deleted_at')->whereNotNull('marks.deleted_at')->where('marks.school_year_id', $school_year_model->id)->where('marks.classe_id', $classe->id)->each(function($mark){

                    if($mark->pupil && $mark->classe && $mark->subject){

                        $mark->restore();
                    }

                });

                $pupils->related_marks()->withTrashed('deleted_at')->whereNotNull('related_marks.deleted_at')->where('related_marks.school_year_id', $school_year_model->id)->where('related_marks.classe_id', $classe->id)->each(function($r_m){

                    if($r_m->classe && $r_m->pupil && $r_m->subject){

                        $r_m->restore();
                    }

                });

            }

        });
    }





}
