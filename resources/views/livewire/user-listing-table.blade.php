<div>
    @if($users)
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                Liste des utilisateurs enregistrés sur la plateforme <span class="text-warning"></span>

                <span class="float-right text-muted"> </span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($users))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Pseudo</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Inscrit depuis</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($users as $k => $u)
                    <tr class="@if($u->hasVerifiedEmail()) text-success @else text-danger @endif">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-left px-2"> 
                            <a class="text-white m-0 p-0 py-1" href="{{route('user_profil', ['id' => $u->id])}}">
                                <span class="d-flex justify-content-between">
                                    <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                        {{ $u->pseudo }}
                                        @if($u->isAdmin())
                                            @if($u->isAdminAs('admin'))
                                                <span class="fa fa-star text-danger"></span>
                                                <span class="fa fa-star text-danger"></span>
                                            @elseif($u->isAdminAs('master'))
                                                <span class="fa fa-star text-danger"></span>
                                                <span class="fa fa-star text-danger"></span>
                                                <span class="fa fa-star text-danger"></span>
                                            @elseif($u->isAdminAs('default'))
                                                <span class="fa fa-star text-danger"></span>
                                            @else
                                                <span class="fa fa-warning text-warning"></span>
                                            @endif

                                        @endif
                                    </span>
                                </span>
                            </a>
                        </td>
                        <td class="text-center"> {{ $u->email }}</td>
                        <td class="text-center"> {{ $u->getRole() ? $u->getRole()->name : null}}</td>
                        <td class="text-center"> {{ $u->getDateAgoFormated($u->created_at) }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer cet utilisateur" wire:click="delete({{$u->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                @if($u->hasVerifiedEmail())
                                    <span title="Marquer comme compte non vérifié" wire:click="markEmailAsUnverified({{$u->id}})" class="text-warning col-3 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-person-x-fill py-2 px-2"></span>
                                </span>
                                @else
                                    <span title="Marquer comme compte déjà vérifié" wire:click="markEmailAsVerified({{$u->id}})" class="text-success col-3 m-0 p-0 cursor-pointer border-right border-left">
                                        <span class="fa fa-check py-2 px-2"></span>
                                    </span>
                                @endif
                                <span wire:click="manageAdminStatus({{$u->id}})" title="Etendre entant que administrateur" class="text-danger border-right border-left col-3 m-0 p-0 cursor-pointer">
                                    <span class="text-secondary cursor-pointer fa fa-user-secret py-2 px-2"></span>
                                </span>
                                <span title="Editer" class="text-danger col-3 border-right border-left m-0 p-0 cursor-pointer">
                                    <span class="text-primary cursor-pointer fa fa-edit py-2 px-2"></span>
                                </span>
                            </span>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>            
    @else
        <div>
            <blockquote class="">
                
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <i class="text-warning small">On a donc affaire à un apprenant ponctuel et exemplaire!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>