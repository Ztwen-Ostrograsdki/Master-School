<div wire:ignore.self class="modal fade lug" id="resetAbsencesAndLatesConfirmation" role="dialog" >
<div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content z-bg-secondary border" style="position: absolute; top:250px;">
        <div class="modal-header">
            <div class="d-flex justify-content-between w-100">
            <h6 class="text-uppercase mr-2 mt-1 text-white">
                Opérations sur les absences et retards
            </h6>
            <div class="d-flex z-bg-secondary justify-content-end w-20">
               <span></span>
            </div>
            </div>
        </div>
        <div class="modal-body m-0 p-0 border z-bg-secondary">
            <div class="">
                <div class="">
                    <h6 class="p-2 text-orange text-center">{{$title}}</h6>
                    <div class=" row w-100 p-0 m-0">
                        <div class="bg-transparent p-0 py-2 col-12">
                            <h6 class="text-warning text-center p-1 m-0 py-1">Voulez-vous vraiment exécuter cette action?</h6> 

                            <div class="col-11 mx-auto m-0 p-0">
                                <h6 class="text-warning p-1 m-0 py-1">Sur la matière: </h6> 
                                <select @if(!auth()->user()->isAdminAs('master')) disabled @endif class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model="subject_id" name="subject_id">
                                    <option value="{{'all'}}">Toutes les matières</option>
                                    @foreach ($subjects as $sub)
                                        <option value="{{$sub->id}}">{{$sub->name}}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            @csrf
                            <div class="d-flex justify-content-center w-100">
                                <form autocomplete="false" method="post" class="mt-3 mr-1 form-group bg-transparent col-4" wire:submit.prevent="confirmForAbsences">
                                    <div class="p-0 m-0 mx-auto d-flex w-100 justify-content-center pb-1 pt-1">
                                        <button class="border border-white w-100 z-scale btn btn--pill bg-danger" type="submit">Absences</button>
                                    </div>
                                </form>
                                <form autocomplete="false" method="post" class="mt-3 mr-1 form-group bg-transparent col-4" wire:submit.prevent="confirmForLates">
                                    <div class="p-0 m-0 mx-auto d-flex w-100 justify-content-center pb-1 pt-1">
                                        <button class="border border-white w-100 z-scale btn btn--pill bg-orange" type="submit">Retards</button>
                                    </div>
                                </form>
                                <form autocomplete="false" method="post" class="mt-3 ml-1 col-3" wire:submit.prevent="cancel">
                                    <div class="p-0 m-0 mx-auto w-100 d-flex justify-content-center pb-1 pt-1">
                                        <button class="border border-white w-100 z-scale btn btn--pill bg-success" type="submit">Annuler</button>
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
