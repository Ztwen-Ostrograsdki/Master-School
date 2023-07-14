<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        

        <div class="d-flex justify-content-start m-0 p-0 my-2">
            <span class="nav-item mx-2">
                <select wire:model="classMapping" class="form-select z-bg-secondary custom-select">
                    <option value=""> Les données à afficher </option>
                    @foreach($dataContent as $class => $datum)
                        <option value="{{$class}}"> {{$datum}} </option>
                    @endforeach
                </select>
            </span>
        </div>


        <div class="my-3 p-2 mx-auto">

            <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark p-3">
                <div class="card-header bg-dark"> 
                    <h5 class="card-title cursor-pointer text-white-50" data-card-widget="collapse">
                        Liste complète des {{ str_replace('Les', '', $dataContent[$classMapping]) }} <span class="text-warning"></span> enregistrés sur la plateforme <span class="text-warning"></span>
                    </h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                    <div class="card-tools">
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid m-0 p-0 w-100">
                        <div class="card-deck w-100 p-0 m-0">
                            <div class="card active" href="#tab_1" data-toggle="tab">
                               @if($classMapping == "App\Models\Pupil")

                                    @livewire('all-pupil-lister', ['level' => $levelName])

                                @elseif($classMapping == "App\Models\Teacher")

                                    @livewire('all-teacher-lister', ['level' => $levelName])

                                @elseif($classMapping == "App\Models\Subject")

                                    @livewire('all-subject-lister', ['level' => $levelName])

                                @elseif($classMapping == "App\Models\Classe")

                                    @livewire('all-classe-lister', ['level' => $levelName])

                                @elseif($classMapping == "App\Models\ClasseGroup")

                                    @livewire('all-classe-group-lister', ['level' => $levelName])

                                @endif
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>