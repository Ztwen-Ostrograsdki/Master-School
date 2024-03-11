<div>
    <div class="p-0 m-0 m-2">
        <div class="mx-auto w-100">
            <h5 class="text-center border rounded py-2 z-bg-secondary text-white-50 text-capitalize">Tableau de gestion des statistiques de classe(s) / promotion(s) / Filière(s)</h5>
        </div>
        <div class="card container-fluid m-0 p-0 w-100 z-bg-secondary border border-dark my-1">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer text-info" data-card-widget="collapse">Veuillez renseigner les détails de la statistique à ressortir...</h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 z-bg-secondary">
                                <div class="info-box-content">
                                    <div class="d-flex justify-content-between">
                                        <form action="" class="w-100 p-2">
                                            <div class="form-group w-100 d-flex justify-content-between">
                                                <div class="w-100 d-flex justify-content-between row m-0 p-0">
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La Filière </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('filial_id') text-danger border border-danger @enderror" wire:model="filial_id" name="filial_id">
                                                            <option value="{{null}}">Sélectionner la Filière </option>
                                                                <option value="{{'all'}}">Toutes les Filières</option>
                                                                @foreach ($filials as $filial)
                                                                    <option value="{{$filial->id}}">La Filière {{ $filial->name }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('filial_id')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La Promotion </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('promotion') text-danger border border-danger @enderror" wire:model="promotion" name="promotion">
                                                            <option value="{{null}}">Sélectionner la promotion </option>
                                                                <option value="{{'all'}}">Toutes les promotions</option>
                                                                @foreach ($promotions as $name => $position)
                                                                    <option value="{{$position}}">La Promotion {{ $name }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('promotion')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La matière </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_selected') text-danger border border-danger @enderror" wire:model="subject_selected" name="subject_selected">
                                                            <option value="{{null}}">Sélectionner la matière </option>
                                                                <option value="{{'all'}}">Toutes les matières</option>
                                                                @foreach ($subjects as $sub)
                                                                    <option value="{{$sub->id}}">{{ $sub->name }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('subject_selected')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le semestre </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('semestre_selected') text-danger border border-danger @enderror" wire:model="semestre_selected" name="semestre_selected">
                                                            <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                                                @foreach ($semestres as $sem)
                                                                    <option value="{{$sem}}">{{$semestre_type . ' ' . $sem}}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('semestre_selected')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-2 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le type d'évaluation </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('type') text-danger border border-danger @enderror" wire:model="type" name="type">
                                                            <option disabled class="" value="{{null}}">Choisissez le type</option>
                                                            @foreach ($types as $key => $type)
                                                                <option value="{{$key}}">{{$type}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-1 m-0 p-0">
                                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">N° </label>
                                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('mark_index') text-danger border border-danger @enderror" wire:model="mark_index" name="mark_index">
                                                            <option disabled class="" value="{{null}}">Choisissez l'évaluation</option>
                                                            @for ($ev = 1; $ev <= $maxLenght; $ev++)
                                                                <option value="{{$ev}}">{{ 'Evaluation ' . $ev}}</option>
                                                            @endfor
                                                        </select>
                                                        @error('mark_index')
                                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group w-100 d-flex justify-content-between">
                                                <input placeholder="Veuillez les différents intervalles de notes à rechercher ..." class="form-control bg-transparent py-1 col-9 text-white border border-white" type="text" name="intervalles" wire:model.defer="intervalles">
                                                <span wire:click="getStats" class="btn btn-primary border border-white px-2 col-2" type="submit">Valider</span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 p-0 m-0">
        <blockquote class="text-primary">
            <h5 class="m-0 h6 p-0 py-2 text-white-50">
                Etude statistique de classe de <span class="text-warning"></span> année-scolaire {{session('school_year_selected')}} 
                <span class="float-right cursor-pointer">Filtrer et lister les apprenants
                    <span class="fa fa-filter {{ $showList ? ' text-success' : 'text-danger' }} "></span>
                </span>
            </h5>
        </blockquote>
    </div>
</div>
