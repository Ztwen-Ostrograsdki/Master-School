<div>
<x-z-modal-generator :topPosition="50" :hasHeader="true" :modalHeaderTitle="'Définition du status administrateur'" :width="6" :icon="'fa fa-user-secret'" :modalName="'adminManagerModal'" :modalBodyTitle="'Edition de status administrateur'">
    @if($user)
    <div class="form-group pb-3 px-2 bg-transparent">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                        <blockquote class="text-info w-100 m-0 my-2">
                            <span class="fa bi-person-check"></span>
                            Utilisateur (e) : 
                            <span class="text-warning">
                                {{ $user->pseudo }} 
                            </span>
                            <span class="text-white-50 float-right text-right"> Email: {{ $user->email }} </span>
                        </blockquote>
                    </div>
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-12 mx-auto justify-content-center m-0 p-0 my-1">
                            <span wire:click="delete" title="Supprimer définitivement cet Utilisateur" class="cursor-pointer btn btn-danger w-100 border py-2 text-center">
                                <span class="bi-trash mx-2"></span>
                                <span class="text-uppercase">supprimer {{ $user->pseudo }}  </span>
                            </span>
                        </div>
                        <div class="col-12 d-flex justify-content-between flex-column row m-0 p-0">
                            <div class="col-12 m-0 p-0 mt-4">
                                <span class="my-1 text-warning d-block">Choisissez le type d'administrateur: </span>
                                <span  title="Etendre en administrateur Standart " class="cursor-pointer btn btn-primary border text-center px-2 {{($user->isAdmin() && $user->isAdminAs('admin')) ? 'd-none' : ''}} ">
                                    <span wire:click="submit('admin')">
                                        Administrateur Standart
                                    </span>
                                </span>

                                <span  title="Etendre en administrateur Master " class="cursor-pointer btn btn-success border text-center px-2 {{($user->isAdmin() && $user->isAdminAs('master')) ? 'd-none' : ''}}">
                                    <span wire:click="submit('master')">
                                        Administrateur Master
                                    </span>
                                </span>

                                <span  title="Etendre en administrateur limité " class="cursor-pointer btn btn-info border text-center px-2 {{($user->isAdmin() && $user->isAdminAs('default')) ? 'd-none' : ''}}">
                                    <span wire:click="submit('default')">
                                        Administrateur Limité
                                    </span>
                                </span>

                                <span  title="Restreindre du status administrateur " class="cursor-pointer btn btn-warning border text-center px-2 {{ !$user->isAdmin() ? 'd-none' : ''}}">
                                    <span wire:click="deleteFromAdmin">
                                        Restreindre du status administrateur 
                                    </span>
                                </span>
                            </div>
                            
                        </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center w-100 mb-2 pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
        @endif
</x-z-modal-generator>
</div>