<div class="m-0 p-0 w-100">
    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >
       <div class="w-100 border my-1 m-0 p-0 z-bg-secondary" style="min-height: 650px;">
            <div class="px-2">
                <div class="m-0 p-0 w-100 my-2 p-2">
                    <hr class="text-warning w-100 m-0 p-0 bg-warning">
                    <h6 class="fa fa-2x py-3 text-white-50">Envoi des épreuves de composition</h6>
                    <hr class="text-warning w-100 m-0 p-0 bg-warning">
                </div>
                <div class="m-0 p-0 w-100 bg-transparent py-2 border row border-dark d-flex justify-content-between">
                    <div class="border col-12 col-lg-4 col-xl-4 my-1 float-left">
                        <div class="m-0 p-0 w-100 my-2 p-2">
                            <hr class="text-warning w-100 m-0 p-0 bg-warning">
                            <h6 style="letter-spacing: 1.2px;" class="fx-20 py-3 text-white-50">Sélectionnées vos épreuves</h6>
                            <hr class="text-warning w-100 m-0 p-0 bg-warning">
                        </div>

                        <div class="m-0 p-0 w-100 my-2 p-2">
                                <div>
                                    <label class="text-orange" for="files">Selectionner vos épreuves</label>
                                    <input class="form-control" id="files" type="file" wire:model="pendingFiles" multiple>
                                </div>

                                <div class="my-3 p-2 d-flex justify-content-start flex-column">
                                    <span class="ml-3 text-warning letter-spacing-12 text-italic text-center" wire:loading wire:target="pendingFiles" >Chargement en cours, veuillez patienter...</span>
                                    @forelse($pendingFiles as $key => $pend)
                                        <span class="m-2 d-flex border rounded">
                                            <table class="m-0 w-100 table-striped table-bordered z-table text-white text-center p-3" style="">
                                                <col>
                                                <col>
                                                <col>
                                                <col>
                                                <col>
                                                <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-0 p-2">
                                                    <th  class="py-2">
                                                        Fichier N°{{ $key + 1 }}
                                                    </th>
                                                    <th class="text-center" >
                                                        <img class="text-center mx-auto" width="30" src="{{asset('icons/unuse-file.png')}}" alt="une image">
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small>{{ mb_substr($pend->getClientOriginalName(), 0, 25) }}</small>
                                                        </span>
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small>{{ number_format(($pend->getSize() / 1000), 2) }} Ko</small>
                                                        </span>
                                                    </th>
                                                    <th >
                                                        <span class="text-white-50 font-italic letter-spacing-12">
                                                            <small> .{{ $pend->extension() }}</small>
                                                        </span>
                                                    </th>
                                                </tr>

                                            </table>                                            
                                        </span>
                                    @empty
                                        <span class="text-cyan mt-5 my-2 p-3 text-center font-italic fx-20 letter-spacing-12 ">Aucun fichier sélectionné</span>
                                    @endforelse
                                </div>

                                <div class="my-1 p-2 d-flex justify-content-start flex-column">
                                    @error('pendingFiles.*')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>

                                @if($pendingFiles)
                                <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                                    <span   wire:click="initiateTransfer" class="text-dark border border-white rounded bg-primary h6 text-center cursor-pointer col-7 z-scale py-2 px-3 ">
                                        <span>Envoyer maintenant</span>
                                        <span class=" fa fa-upload mx-2"></span>
                                    </span>

                                </div>
                                @else
                                <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                                    <strong  class="text-dark border h6 border-warning rounded bg-secondary text-center cursor-pointer col-7 z-scale py-2 px-3 ">Veuillez sélectionner des épreuves</strong>
                                </div>
                                @endif


                        </div>
                    </div>



                    <div class="border my-1 col-12 col-lg-7 col-xl-7 float-right">
                        <div class="m-0 p-0 w-100 my-2 p-2">
                            <hr class="text-warning w-100 m-0 p-0 bg-warning">
                            <h6 style="letter-spacing: 1.2px;" class="fx-20 py-3 text-white-50">Détails du processus</h6>
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
                                    <tr class="bg-secondary-light-0 fx-20 font-italic" style="letter-spacing: 1.2px;">
                                        <th colspan="3" class="py-3"> Total </th>
                                        <th> 12 </th>
                                        <th> 10 </th>

                                    </tr>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
