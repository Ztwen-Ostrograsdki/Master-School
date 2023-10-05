<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark "> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">DETAILS SUR LE CALENDRIER SCOLAIRE </h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                  <div class="card-tools">
                    
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Options <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir le calendrier scolaire</a>
                            
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-primary">
                                <span class="info-box-icon"><i class="fa fa-user-nurse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les Evènements</span>
                                    <span class="info-box-number">90 </span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                  <span class="info-box-text">Jours fériés</span>
                                  <span class="info-box-number">14</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-danger">
                                <span class="info-box-icon"><i class="fa fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Evaluations</span>
                                    <span class="info-box-number">10</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="far fa-comment"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Conseils / AP</span>
                                    <span class="info-box-number">12</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>

                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
          <!-- Custom Tabs -->
            <div class="card">
                <div class="card-header d-flex p-0">
                    @if($periods)
                        <ul class="nav nav-pills ml-auto p-2">
                            <li wire:click="setActiveSection('calendrier_semestre')" class="nav-item">
                                <a class="nav-link @if(session()->has('calendar_section') && session('calendar_section') == 'calendrier_semestre') active @elseif(!session()->has('calendar_section')) active @endif border border-white" href="#tab_cal_semestre" data-toggle="tab">Calendrier Semestres</a>
                            </li>

                            <li wire:click="setActiveSection('calendrier_devoirs')" class="nav-item mx-1"><a class="nav-link border border-white @if(session()->has('calendar_section') && session('calendar_section') && session('calendar_section') == 'calendrier_devoirs') active @endif" href="#tab_cal_devoirs" data-toggle="tab">Calendrier Devoirs</a>
                            </li>
                            
                        </ul>
                    @else
                        <h3 class="card-title ml-auto p-3 float-right text-warning">
                            <span class="bi-lock mx-2"></span>
                            <span>La classe de <b class="text-orange"> </b> est vide pour l'année scolaire <b class="text-orange"> </b> </span>
                        </h3>
                    @endif
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        @if (!$periods)
                        <div class="w-100 border rounded border-warning p-2 m-2">
                            <blockquote class="text-info">
                                <h5>
                                    <span>
                                        Veuillez cliquer pour générer un calendrier des activités scolaires de votre prestigieuse école {{ session('school_year_selected') }}
                                    </span>
                                </h5>
                            </blockquote>
                        </div>
                        @endif
                        @if($periods && $school_year_model)
                            <div class="tab-pane calendrier_semestre @if(session()->has('calendar_section') && session('calendar_section') == 'calendrier_semestre') active @elseif(!session()->has('calendar_section')) active @endif" id="tab_cal_semestre">
                                <div>
                                    @livewire('semestre-calendars', ['school_year' => $school_year_model->school_year])
                                </div>
                            </div>
                            <div class="tab-pane calendrier_devoirs @if(session()->has('calendar_section') && session('calendar_section') && session('calendar_section') == 'calendrier_devoirs') active @endif" id="tab_cal_devoirs">
                                PAGE 2 -- CALENDRIER DEVOIRS
                            </div>
                        @endif
                    </div>
                </div><!-- /.card-body -->
            </div>
          <!-- ./card -->
        </div>
        <!-- /.col -->
    </div>
</div>
