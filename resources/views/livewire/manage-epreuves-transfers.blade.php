<div class="m-0 p-0 w-100">
    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >
       <div class="w-100 my-1 m-0 p-0 z-bg-secondary" style="min-height: 650px;">
            <div class="p-4">
                <div class="m-0 p-0 w-100 my-2 p-2">
                    <hr class="text-warning w-100 m-0 p-0 bg-warning">
                    <h6 class="fa fa-2x py-3 text-white-50">Envoi des épreuves de composition</h6>
                    <hr class="text-warning w-100 m-0 p-0 bg-warning">
                </div>

               <div class="m-0 p-0 w-100 bg-secondary-light-3 my-2 border border-white  py-2 row d-flex justify-content-between px-2">
                    <div class="m-0 p-0 w-100 my-1 p-2">
                        <hr class="text-warning w-100 m-0 p-0 bg-warning">
                        <h6 style="letter-spacing: 1.2px;" class="fx-20 py-1 text-warning text-center">Préciser les infos de votre épreuve</h6>
                        <hr class="text-warning w-100 m-0 p-0 bg-warning">
                    </div>

                    <div class="m-0 p-0 w-100 bg-transparent py-2 row d-flex justify-content-between">
                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Choisissez le {{$semestre_type}}</label>
                            <select wire:model.defer="semestre" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="bg-secondary-light-0" value="{{null}}">Choisissez</option>
                                @foreach($semestres as $sem)
                                    <option class="bg-secondary-light-0" value="{{$sem}}"> {{$semestre_type . ' ' . $sem}}  </option>
                                @endforeach
                            </select>
                            @error('semestre')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Choisissez la matière</label>
                            <select wire:model.defer="subject_id" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="bg-secondary-light-0" value="{{null}}">Choisissez la matière</option>
                                @foreach($subjects as $sub)
                                    <option class="bg-secondary-light-0" value="{{$sub->id}}"> {{$sub->name}} </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Choisissez la classe</label>
                            <select wire:model.defer="classe_id" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="bg-secondary-light-0" value="{{null}}">Choisissez la classe</option>
                                @foreach($classes as $cl)
                                    <option class="bg-secondary-light-0" value="{{$cl->id}}"> {{$cl->name}} </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="m-0 p-0 w-100 bg-transparent py-2 row d-flex justify-content-between">

                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Choisissez la promotion</label>
                            <select wire:model.defer="classe_group_id" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="bg-secondary-light-0" value="{{null}}">Choisissez la promotion</option>
                                @foreach($classe_groups as $clg)
                                    <option class="bg-secondary-light-0" value="{{$clg->id}}"> {{$clg->name}} </option>
                                @endforeach
                            </select>
                            @error('classe_group_id')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Définissez la dureé</label>
                            <input class="form-control bg-transparent border border-white text-white" type="text" wire:model.defer="duration" placeholder="2 heures de compositions">
                            @error('duration')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="col-3 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Le type d'évaluation</label>
                            <select wire:model.defer="target" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="bg-secondary-light-0" value="{{null}}">Choisissez le type</option>
                                @foreach($targets as $tg => $name)
                                    <option class="bg-secondary-light-0" value="{{$tg}}"> {{$name}} </option>
                                @endforeach
                            </select>
                            @error('target')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="col-2 m-0 p-0 mb-2">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer letter-spacing-12">Evaluation N°</label>
                            <select wire:model.defer="index" class="px-2 form-select custom-select text-white bg-transparent w-100" >
                                <option class="" value="{{null}}">Avaluation N°</option>
                                @for($i = 1; $i <= 30; $i++)
                                    <option class="bg-secondary-light-0" value="{{$i}}"> N° {{$i}} </option>
                                @endfor
                            </select>
                            @error('index')
                                <small class="text-orange letter-spacing-12 text-italic">{{$message}}</small>
                            @enderror
                        </div>

                    </div>

                </div>
                <div class="m-0 p-0 w-100 bg-transparent py-2 p-3 mx-auto col-11 col-lg-7 col-xl-7 row border border-dark border-0-cyan border-1-cyan d-flex justify-content-center">
                    <div class=" col-12 mx-auto border">
                        <div class="m-0 p-0 w-100 my-2 p-2">
                                <div>
                                    <label class="text-orange" for="files">Selectionner votre épreuve</label>
                                    <input placeholder="Sélectionner votre épreuve" class="form-control bg-transparent text-white letter-spacing-12 text-italic border border-white" id="files" type="file" wire:model="pendingFile">
                                </div>

                                <div class="my-3 p-2 d-flex justify-content-start flex-column">
                                    <span class="ml-3 text-warning letter-spacing-12 text-italic text-center" wire:loading wire:target="pendingFile" >Chargement en cours, veuillez patienter...</span>
                                    @if($pendingFile)
                                        <span class="m-2 d-flex border rounded">
                                            <table class="m-0 w-100 table-striped table-bordered z-table text-white text-center p-3" style="">
                                                <col>
                                                <col>
                                                <col>
                                                <col>
                                                <col>
                                                <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-0 p-2">
                                                    <th  class="py-2">
                                                        Fichier 
                                                    </th>
                                                    <th class="text-center" >
                                                        <img class="text-center mx-auto" width="30" src="{{asset('icons/unuse-file.png')}}" alt="une image">
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small>{{ mb_substr($pendingFile->getClientOriginalName(), 0, 25) }}</small>
                                                        </span>
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small>{{ number_format(($pendingFile->getSize() / 1000), 2) }} Ko</small>
                                                        </span>
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small> .{{ $pendingFile->extension() }}</small>
                                                        </span>
                                                    </th>
                                                </tr>

                                            </table>                                            
                                        </span>
                                    @else
                                        <span class="text-cyan text-center font-italic fx-20 letter-spacing-12 ">Aucun fichier sélectionné</span>
                                    @endif
                                </div>

                                <div class="my-1 p-2 d-flex justify-content-start flex-column">
                                    @error('pendingFile')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>

                                @if($pendingFile)
                                <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                                    <span   wire:click="initiateTransfer" class="text-dark border border-white rounded bg-primary h6 text-center cursor-pointer col-7 z-scale py-2 px-3 ">
                                        <span>Envoyer maintenant</span>
                                        <span class=" fa fa-upload mx-2"></span>
                                    </span>

                                </div>
                                @else
                                <div style="opacity: 0.4;" class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                                    <strong  class="text-dark border h6 border-warning rounded bg-secondary text-center cursor-pointer col-7 py-2 px-3 ">Veuillez sélectionner votre épreuve</strong>
                                </div>
                                @endif
                        </div>
                    </div>



                    <div class="border my-1 col-12 mx-auto">
                        <div class="m-0 p-0 w-100 my-1 p-2">
                            <hr class="text-warning w-100 m-0 p-0 bg-warning">
                            <h6 style="letter-spacing: 1.2px;" class="fx-20 text-white-50">Détails du processus</h6>
                            <hr class="text-warning w-100 m-0 p-0 bg-warning">
                        </div>
                        <div class="container-fluid m-0 p-0 w-100">
                            <div class="mx-auto w-100 my-2 p-2">
                                <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">

                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-3">
                                        <th  class="py-2">No</th>
                                        <th >Epreuves</th>
                                        <th >Statut</th>
                                        <th >Taille</th>
                                        <th >Action</th>
                                    </tr>
                                    @foreach($tables as $name => $size)

                                        <tr>
                                            <th>{{$loop->iteration}}</th>
                                            <th class="py-2 pl-2 text-left">{{$name}}</th>
                                            <th> - </th>
                                            <th> {{$size}} </th>
                                            <th> 
                                                <span class="btn btn-success w-100">
                                                    <span class="fa fa-check"></span>
                                                    <span class="">Envoyé</span>
                                                </span>
                                            </th>
                                        </tr>
                                    @endforeach
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
