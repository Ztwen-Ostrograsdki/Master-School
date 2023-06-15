<div>
    @if($users)
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                Liste des utilisateurs bloqués ou verrouillés enregistrés de la plateforme <span class="text-warning"></span>

                <span class="float-right text-muted"> </span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($users))
        <table class="w-99 mx-auto m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Pseudo</th>
                <th>Statut</th>
                <th>Bloqués depuis</th>
                <th>Demande</th>
                <th>Token SFFCm</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($users as $k => $u)
                    <tr class="text-white-50">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-left px-2"> 
                            <a class="text-white m-0 p-0 py-1" href="{{route('user_profil', ['id' => $u->id])}}">
                                <span class="d-flex justify-content-between">
                                    <span class="mx-2 text-white d-none d-lg-inline d-xl-inline text-small ">
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

                        <td class="text-center"> {{ $u->getRole() ? $u->getRole()->name : null}}</td>
                        <td class="text-center"> {{ $u->getDateAgoFormated($u->updated_at) }}</td>
                        <td class="text-center"> {{ $u->lockedRequests ? $u->lockedRequests->message : ' - ' }}</td>
                        <td class="text-center"> {{ $u->unlock_token ? 'FF' . mb_substr($u->unlock_token, 0, 7) : 'EE2gfdede' . rand(147, 88897) }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer cette demande" wire:click="delete({{$u->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                <span wire:click="blockerManager({{$u->id}})" title="Débloquer l'utilisateur {{$u->pseudo}}" class="text-warning col-4 border-left m-0 p-0 cursor-pointer">
                                    <span class="text-warning cursor-pointer fa fa-unlock py-2 px-2"></span>
                                </span>
                                <span wire:click="generateAndSendKeyToUser({{$u->id}})"  title="Envoyez un code de vérification à l'utilisateur {{$u->pseudo}}" class="text-success col-4 border-left m-0 p-0 cursor-pointer">
                                    <span class="text-success cursor-pointer fa bi-envelope-open py-2 px-2"></span>
                                </span>
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>            
    @else
        <div>
            <div class="d-flex justify-content-center mx-auto w-100">
                <span class="fa fa-trash text-muted fa-8x"></span>
            </div>
            <blockquote class="text-warning">
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <i class="text-warning small">La liste est vide!!!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>