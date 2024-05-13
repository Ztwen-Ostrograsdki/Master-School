<div>
    <li class="nav-item dropdown" title="{{count($onlines_users)}} utilisateurs ont présentement connectés">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far bi-person"></i>
          <span class="badge badge-danger navbar-badge">{{count($onlines_users)}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right bg-secondary-light-0 border border-orange">

            <a href="#" class="dropdown-item">
            <!-- Message Start -->
                <div class="media">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                            <span class="text-warning">Les utilisateurs connectés</span>
                            <span class="float-right text-sm text-success"><i class="fas fa-users"></i></span>
                        </h3>
                        <p class="text-sm text-muted"><i class="far bi-person-check mr-1"></i> Présentement</p>
                  </div>
                </div>
            <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>

            @if($users && count($users))
            
                @foreach($users as $user)
                    <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                        <div class="media">
                            <img src="{{$user->__profil('250')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                  {{$user->pseudo}}
                                    <span class="float-right text-sm text-success"><i class="fas fa-circle"></i></span>
                                </h3>
                                <p class="text-sm text-orange">{{ $user->email }}</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> En ligne</p>
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
                        <img src="/myassets/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Auncun utilisateur 
                                <span class="float-right text-sm text-success"><i class="fas fa-circle"></i></span>
                            </h3>
                            <p class="text-sm">n'est connecté pour le moment</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i></p>
                      </div>
                    </div>
                <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
            @endif
        </div>
      </li>
</div>
