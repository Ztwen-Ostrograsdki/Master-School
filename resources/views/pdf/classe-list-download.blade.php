<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- <meta name="caffeinated" content="false"> --}}

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @include("components.mycomponents.styles") {{-- chargement des styles --}}
    </head>
        {{-- chargement des modals --}}
        <body class="font-sans antialiased" style="background-color: lightslategray;">
        @include('components.mycomponents.loader') {{-- chargement du loader --}}
        @livewire('header') {{-- chargement du header --}}
        <div class=" bg-gray-100 border" style="background-color: rgb(167, 193, 219); min-height: 150vh !important;">
            <!-- Page Content -->
            <div class="col-10 mx-auto p-3">
                <div>
    @if($classe)
        <div class="w-75 mx-auto mt-3">
            <span  class="btn bg-orange border border-white" title="Ajouter des aprrenants à cette classe">
                <span class="fa fa-user-plus"></span>
                <span>Multiple Ajout</span>
            </span> 
            <span class="btn btn-primary border border-white" title="Ajouter un aprrenant à cette classe">
                <span class="fa fa-user-plus"></span>
                <span>Ajouter</span>
            </span>
            <span  class="btn mx-2 btn-secondary border border-white" title="Editer les matières de cette classe">
                <span class="fa fa-edit"></span>
                <span>Editer</span>
            </span>
            <span  class="btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de cette classe...">
                <span class="fa fa-print"></span>
                <span>Impr.</span>
            </span>

        </div>
        <div class="w-100 m-0 p-0 mt-3">
        @if($pupils && count($pupils) > 0)
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Nom et Prénoms</th>
                <th class="">Sexe</th>
                <th>Matricule</th>
                <th>Inscrit depuis</th>
            </thead>
            <tbody>
                @foreach($pupils as $k => $p)
                    <tr class="">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2">
                            <span class="d-flex w-100">
                                <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex">
                                        <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                        </td>
                        <td class="text-center cursor-pointer" title="Doublecliquer pour changer le sexe">
                            {{ $p->getSexe() }}
                        </td>
                        <td class="text-center">
                            {{ $p->matricule }}
                        </td>
                        <td class="text-center">
                            {{ str_ireplace("Il y a ", '', $p->getDateAgoFormated(true)) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>   
        @endif
        @if(!$pupils)
            <div class="my-2 p-2 text-center border rounded">
                <h6 class="mx-auto p-3">
                    <h1 class="m-0 p-0">
                        <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                    </h1>
                    Il parait qu'aucune donnée n'est disponible pour cette classe de 
                    <span class="text-warning">{{ session('classe_selected') }}</span> 
                    pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                    

                    <blockquote class="text-info">
                        Veuillez sectionner une autre année scolaire
                    </blockquote>
                </h6>
            </div>
        @endif
    @endif                                                
</div>
</div>

            </div>
        </div>
        @include('sweetalert::alert')
        <script src="//{{Request::getHost()}}:6001/socket.io/socket.io.js" ></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script>
        </script>
        @include("components.mycomponents.scripts") {{-- chargement des scripts js --}}
    </body>
</html>
