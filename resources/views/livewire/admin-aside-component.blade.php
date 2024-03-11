<div>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
       @if($has_school)
        <!-- Brand Logo -->
        <a href="{{route('admin')}}" class="brand-link">
          <img src="/myassets/images/product_02.jpg" alt="AdminLTE Logo" class="border border-white brand-image " style="opacity: .8;">
          <span class="brand-text font-weight-light">{{ $school_name ? $school_name : 'ZtweN-School' }}</span>
        </a>

        <!-- Sidebar -->
        @auth
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image mt-2">
                    <img src="{{auth()->user()->__profil('250')}}" class="img-circle" alt="User Image">
                </div>
                <div class="info mt-2">
                    <a href="{{route('user_profil', ['id' => auth()->user()->id])}}" class="d-block">
                        {{ auth()->user()->pseudo }}
                        <span class="text-success fa fa-circle"></span>
                        <small class="text-white-50">Connecté</small>
                      </a>
                </div>
            </div>
          @endauth
    
          <!-- SidebarSearch Form -->
          <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar">
                  <i class="fas fa-search fa-fw"></i>
                </button>
              </div>
            </div>
          </div>
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="{{route('admin')}}" class="nav-link @isRoute('admin') active @endisRoute">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Tableau de bord
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link @isRoute('classe_pupils') active @endisRoute">
                  <i class="nav-icon bi-people"></i>
                  <p class="text-bold">
                    Les Apprenants
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-info right">{{$pupils->count()}}</span>
                  </p>
                </a>

                @foreach ($levels as $level)
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-plus"></i>
                      <p>
                        {{ $level->getName() }}
                        <i class="fas fa-angle-left right"></i>
                        <span class="badge badge-info right">
                          {{ count($level->getLevelPupils()) }}
                        </span>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{route('pupil_listing', ['slug' => strtolower($level->nameInFrench())])}}" class="nav-link ">
                          <i class="fas nav-icon fa-plus"></i>
                          <p> Liste complète </p>
                          <span class="badge badge-info right">
                            {{ count($level->getLevelPupils()) }}
                          </span>
                        </a>
                      </li>
                      @foreach ($classes as $c)
                        @if($c->level_id == $level->id)
                        <li class="nav-item">
                          <a href="{{route('classe_pupils', [urlencode($c->slug)])}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p> 
                              <span>{{ $c->name }}</span>
                              <span class="badge badge-info right">
                                {{ count($c->getClassePupils()) }}
                              </span>
                            </p>
                          </a>
                        </li>
                        @endif
                      @endforeach
                    </ul>
                  </li>
                </ul>
                @endforeach
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" wire:click="addNewPupil" class="nav-link text-info">
                      <i class="nav-icon fas fa-user-plus"></i>
                      <p>
                          Nouvel apprenant
                          <i class="fas fa-plus right"></i>
                      </p>
                    </a>
                  </li>
                  <li class="nav-item text-primary">
                    <a href="{{route('multiple_pupil_insertion')}}" class="nav-link">
                      <i class="nav-icon fas bi-person-plus"></i>
                      <p>
                          Ajout multiple
                          <i class="fas fa-plus right"></i>
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link @isRoute('classe_teachers') active @endisRoute">
                  <i class="nav-icon bi-people"></i>
                  <p class="text-bold">
                    Les Enseignants
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-success right">{{$teachers->count()}}</span>
                  </p>
                </a>
                @foreach ($levels as $level)
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-plus"></i>
                      <p>
                        {{ $level->getName() }}
                        <i class="fas fa-angle-left right"></i>
                        <span class="badge badge-success right">{{$school_year_model ? $school_year_model->teachers()->where('level_id', $level->id)->count() : 0}}</span>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @foreach ($classes as $c)
                        @if($c->level_id == $level->id)
                          <li class="nav-item">
                            <a href="{{route('classe_teachers', [urlencode($c->slug)])}}" class="nav-link">
                              <i class="far fa-circle nav-icon"></i>
                              <p> De la {{ $c->name }} </p>
                            </a>
                          </li>
                        @endif
                      @endforeach
                    </ul>
                  </li>
                </ul>
                @endforeach
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('teacher_listing')}}" class="nav-link @isRoute('teacher_listing') active @endisRoute" class="nav-link">
                      <i class="nav-icon fas fa-plus"></i>
                      <p>
                        Liste complète
                        <i class="fas fa-angle-down right"></i>
                        <span class="badge badge-success right">{{count($teachers)}}</span>
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi-house"></i>
                  <p class="text-bold">
                    Les Classes
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-warning right">{{$classes->count()}}</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('classe_listing')}}" class="nav-link @isRoute('classe_listing') active @endisRoute">
                      <i class="far fa-circle nav-icon"></i>
                      <p> Liste complète </p>
                    </a>
                  </li>
                </ul>
                @foreach ($levels as $level)
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-plus"></i>
                      <p>
                        {{ $level->getName() }}
                        <i class="fas fa-angle-left right"></i>
                        <span class="badge badge-warning right">{{ count($level->level_classes(session('school_year_selected'))) }}</span>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @foreach ($classes as $c)
                        @if($c->level_id == $level->id)
                          <li class="nav-item">
                            <a href="{{route('classe_profil', [urlencode($c->slug)])}}" class="nav-link">
                              <i class="far fa-circle nav-icon"></i>
                              <p> {{ $c->name }} </p>
                            </a>
                          </li>
                        @endif
                      @endforeach
                    </ul>
                  </li>
                </ul>
                @endforeach
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" wire:click="createNewClasse" class="nav-link text-warning">
                      <i class="nav-icon fas fa-school"></i>
                      <p>
                        Nouvelle classe
                        <i class="fas fa-plus right"></i>
                        <span class="badge badge-info right"></span>
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link @isRoute('classe_group_profil') active @endisRoute">
                  <i class="nav-icon fa fa-filter"></i>
                  <p class="text-bold">
                    Les Promotions
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-secondary right">{{$classe_groups->count()}}</span>
                  </p>
                </a>
                @foreach ($levels as $level)
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-plus"></i>
                      <p>
                        {{ $level->getName() }}
                        <i class="fas fa-angle-left right"></i>
                        <span class="badge badge-secondary right">{{ count($level->classe_groups) }}</span>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @foreach ($classe_groups as $promotion)
                        @if($promotion->level_id == $level->id)
                          <li class="nav-item">
                            <a href="{{route('classe_group_profil', [urlencode($promotion->name)])}}" class="nav-link">
                              <i class="far fa-circle nav-icon"></i>
                              <p> {{ $promotion->name }} </p>
                            </a>
                          </li>
                        @endif
                      @endforeach
                    </ul>
                  </li>
                </ul>
                @endforeach
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" wire:click="addNewClasseGroup" class="nav-link text-info">
                      <i class="nav-icon fas fa-filter"></i>
                      <p>
                        Nouvelle Promotion
                        <i class="fas fa-plus right"></i>
                        <span class="badge badge-info right"></span>
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                     Les parents
                    <i class="fas fa-angle-left right"></i>
                    <span class="badge badge-secondary right">{{ count($parents) }}</span>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('parents_listing')}}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Liste complète</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Nouveau</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Autres</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link @isRoutes(['user_listing', 'user_listing_by_target']) active @endisRoutes">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    Utilisateurs
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('user_listing')}}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Tous</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Confirmés</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Non Confirmés</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{route('user_listing_by_target', ['target' => 'bloques'])}}" class="nav-link @isRoute('user_listing_by_target') active @endisRoute">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Bloqués</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-header text-uppercase text-warning">Historiques</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-book-open"></i>
                  <p>
                    Lire l'historique
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Récente</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Enseignants</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Aprenants</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Utilisateurs</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Notes</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Payements</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Sécurité</p>
                    </a>
                  </li>
                </ul>
              </li>
              

              <li class="nav-header text-uppercase text-orange">Gestionnaire/Comptabilité</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-tree"></i>
                  <p>
                    Les scolarités
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Maternelle</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Primaire</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Secondaire</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Superieure</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Nouvelles</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Fermées</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                    Parents
                    <i class="fas fa-angle-left right"></i>

                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Liste complète</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Nouveau</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Autres</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-header text-uppercase text-primary">Gestionnaires epreuves</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-book-open"></i>
                  <p>
                    De composition
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @foreach($levels as $l)
                  <li class="nav-item">
                    <a href="{{route('epreuves_de_composition_envoyes')}}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{$l->getName()}}</p>
                    </a>
                  </li>
                  @endforeach
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas bi-bank"></i>
                  <p>
                    Banques
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Tous</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Nouveau</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Autres</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-header text-uppercase text-warning">Outils</li>
              <li class="nav-item">
                <a href="{{route('statistics')}}" class="nav-link @isRoute('statistics') active @endisRoute">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Statistiques
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('data_manager_primary')}}" class="nav-link @isRoute('data_manager_primary') active @endisRoute">
                  <i class="nav-icon fas fa-school"></i>
                  <p>
                    Gest. Scolaire Primaire
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('data_manager_secondary')}}" class="nav-link @isRoute('data_manager_secondary') active @endisRoute">
                  <i class="nav-icon fas fa-school"></i>
                  <p>
                    Gest. Scolaire Secondaire
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              @foreach($levels as $level)
              <li class="nav-item">
                <a href="{{route('polyvalente_classe', ['slug' => strtolower($level->nameInFrench())])}}" class="nav-link @isRoute('polyvalente_classe') active @endisRoute">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    Polyvalente {{$level->nameInFrench()}}
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              @endforeach
              
              <li class="nav-item">
                <a href="{{route('subject_listing')}}" class="nav-link @isRoute('subject_listing') active @endisRoute">
                  <i class="nav-icon fas fa-book-open"></i>
                  <p>
                    Les matières
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('time_plans', ['school_year' => str_replace(' - ', '-', session('school_year_selected'))])}}" class="nav-link @isRoute('time_plans') active @endisRoute">
                  <i class="nav-icon fas fa-clock"></i>
                  <p>
                    Emplois du temps
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('school_calendar', ['school_year' => str_replace(' - ', '-', session('school_year_selected'))])}}" class="nav-link @isRoute('school_calendar') active @endisRoute">
                  <i class="nav-icon fas fa-calendar-alt"></i>
                  <p>
                    Calendrier
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin_teacher_security_actions')}}" class="nav-link @isRoute('admin_teacher_security_actions') active @endisRoute">
                  <i class="nav-icon fas bi-tools"></i>
                  <p>
                    Securité
                    <span class="badge badge-info right"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon far fa-image"></i>
                  <p>
                    Gallerie
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon far fa-envelope"></i>
                  <p>
                    Boite Mail
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Receptions</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Envoyées</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Lus</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-book-open"></i>
                  <p>Aide</p>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      @else
        <a href="index3.html" class="brand-link">
          <img src="/myassets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Créez votre école</span>
        </a>
    
        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <img src="/myassets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
              <a href="#" class="d-block">ZtweN Oströ</a>
            </div>
          </div>
    
          <!-- SidebarSearch Form -->
          <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar">
                  <i class="fas fa-search fa-fw"></i>
                </button>
              </div>
            </div>
          </div>
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="{{route('admin')}}" class="nav-link active">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Tableau de bord
                  </p>
                </a>
              </li>
            </ul>
          </nav>
          <h6 class="h6 mt-5 text-warning text-wrap">
            Aucune donnée n'est disponible
          </h6>
          <div class="w-100 p-2 mx-auto d-flex justify-content-center">
              <span wire:click="throwSchoolBuiding" class="btn btn-primary p-2 w-75 border border-white">Créer mon école</span>
          </div>
        </div>
      @endif
      </aside>
</div>
