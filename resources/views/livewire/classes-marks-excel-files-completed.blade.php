<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning p-0">
                <hr class=" w-100 m-0 p-0 bg-primary">
                <h6 style="letter-spacing: 1.2px" class="w-100 py-2 m-0 fx-17 text-orange text-right px-2 mr-2 font-italic"> Les fichiers de notes renseignés de la classe {{ $classe ? $classe->name : '' }} </h6>
                <hr class=" w-100 m-0 p-0 bg-primary">
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            <div class="w-100 my-1 mt-2 d-flex justify-content-between mb-2">
                <div class="d-flex justify-content-start m-0 p-0">
                    <span class="nav-item mx-2">
                        <select  class="form-select z-bg-secondary custom-select">
                            <option value="{{null}}"> Lister  </option>
                            <option value="all"> Liste complète </option>
                        </select>
                    </span>
                </div>

            </div>

            <div class="card-body z-bg-secondary">
                <div class="w-100 m-0 p-0 mt-1">
                    {{-- {{ dd($classe_files); }} --}}
                    @if(count($classe_files) > 0)

                       <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <tr class="text-center fx-15 text-orange bg-secondary-light-0" style="letter-spacing: 1.2px;">
                                <td class="py-3">No</td>
                                <td>
                                    Fichier
                                    <span>(type)</span>
                                </td>
                                <td>Matière</td>
                                <td>Classe</td>
                                <td>Auteur</td>
                                <td>Téléchargements</td>
                                <td>Type</td>
                                <td>Taille</td>
                                <td>Action</td>
                            </tr>

                            @foreach($classe_files as $cf)

                            
                            <tr class="text-center">
                                <td class="text-center ">{{ $loop->iteration }}</td>
                                <td class="text-left px-2 ">
                                    <span class="d-block">
                                        {{ $cf->name }}
                                        <span class="float-right text-orange">( {{str_replace('.', '', $cf->extension)}} ) </span>
                                    </span>
                                    <small class="float-right text-warning">
                                        Envoyé le {{ $cf->__to(null, false, true) }}
                                    </small>

                                </td>
                                <td class="">{{ $cf->subject->name}}</td>
                                <td class="">{{ $cf->classe->name}}</td>
                                <td class=" ">{{ $cf->user->pseudo }}</td>
                                <td class=" ">
                                    {{ $cf->downloaded_counter }}
                                </td>
                                <td>
                                    <span class="fa bi-filetype-{{str_replace('.', '', $cf->extension)}} fx-17"></span>
                                </td>
                                <td>
                                    {{ number_format((Illuminate\Support\Facades\File::size($cf->getFullPath()) / 1000), 2)   }} Ko
                                </td>
                                <td class="p-0 m-0">
                                   <div class="d-flex justify-content-around">
                                        <div wire:click="downloadTheFile({{$cf->id}})" title="Télécharger le fichier" class="btn btn-primary">
                                            <span  class="fa fa-download"></span>
                                       </div>                                        
                                       <div wire:click="downloadThePDFFormat({{$cf->id}})" title="Convertir et télecharger en PDF" class="btn btn-primary border border-warning">
                                            <span class="fa bi-filetype-pdf mr-2"></span>
                                            <span class="fa fa-download"></span>
                                       </div>
                                       <div wire:click="deleteTheFile({{$cf->id}})" title="Supprimer le fichier" class="btn btn-danger">
                                            <span class="fa fa-trash"></span>
                                       </div>
                                       @if(!$cf->secure)
                                           <div wire:click="secureTheFile({{$cf->id}})" title="Bloquer le téléchargement du fichier"  class="btn btn-warning">
                                                <span class="fa fa-lock"></span>
                                           </div>
                                       @else
                                            <div wire:click="unsecureTheFile({{$cf->id}})" title="Débloquer le téléchargement du fichier"  class="btn btn-success">
                                                <span class="fa fa-unlock"></span>
                                           </div>

                                       @endif
                                   </div>
                                </td>
                                
                            </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
