<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Informations Générales {{ $classe_group ? 'de la Promotion ' . $classe_group->name : "" }}</h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="fa fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Effectif</span>
                                    <span class="info-box-number"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-primary">
                                <span class="info-box-icon"><i class="fa fa-user-nurse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les Notes</span>
                                    <span class="info-box-number">90 000</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                  <span class="info-box-text">Scolarités</span>
                                  <span class="info-box-number">92 050</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-danger">
                                <span class="info-box-icon"><i class="fa fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Emploi du temps</span>
                                    <span class="info-box-number">114 381</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="far fa-comment"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les enseignants</span>
                                    <span class="info-box-number">163 921</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
