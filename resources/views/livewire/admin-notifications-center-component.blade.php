<div>
    <li class="nav-item dropdown ">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far bi-envelope"></i>
          <span class="badge badge-danger navbar-badge">{{ $all_notifications }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right bg-secondary-light-0 border border-orange">

            <a href="#" class="dropdown-item">
            <!-- Message Start -->
                <div class="media">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                            Les notifications en cours
                            <span class="float-right text-sm text-success"><i class="fas fa-enveloppe"></i></span>
                        </h3>
                        <p class="text-sm text-muted"><i class="far bi-person-check mr-1"></i> Présentement</p>
                  </div>
                </div>
            <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>

            <div class="border border-warning p-1 m-1 rounded">

                @if($lockedRequests && count($lockedRequests))

                    <a href="#" class="text-warning nav-link list-unstyled px-1">Les demandes comptes bloqués ( {{ count($lockedRequests) }} ) </a>
                    <hr class="bg-warning w-100 m-0 p-0 my-1">

                    @foreach($lockedRequests as $lockedRequest)
                
                        <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                            <div class="media">
                                <img src="{{ $lockedRequest->user->__profil('250') }}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                      {{ $lockedRequest->user->pseudo }}
                                        <span class="float-right text-sm text-success"><i class="fas fa-user-shield"></i></span>
                                    </h3>
                                    <p class="text-sm text-orange">{{ mb_substr($lockedRequest->message, 0, 10) }}</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> {{ $lockedRequest->getDateAgoFormated($lockedRequest->created_at) }} </p>
                                    <p class="float-right text-right">
                                        <span wire:click="deleteLockedRequest({{$lockedRequest->id}})" class="fa fa-trash text-orange cursor-pointer z-scale" title="Supprimer cette requête"></span>
                                        <span wire:click="solvedLockedRequest({{$lockedRequest->id}})" class="fa fa-check text-success mx-2 cursor-pointer z-scale" title="Débloquer le compte de {{ $lockedRequest->user->pseudo }} "></span>
                                    </p>
                                </div>
                            </div>
                        <!-- Message End -->
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @else

                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <span class="fa fa-key mt-1 text-warning mr-1"></span>
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Aucune demande
                                    <span class="float-right text-sm text-orange ml-1"><i class="fas fa-user-shield"></i></span>
                                </h3>
                                <p class="text-sm">de deblocages de </p>
                                <p class="text-sm">comptes n'est en cours </p>
                          </div>
                        </div>
                    <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

            </div>
        </div>
      </li>
</div>
