<table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                                <thead class="text-white text-center">
                                    <th class="py-2 text-center">#ID</th>
                                    <th class="py-1">Nom et Prénoms</th>
                                    <th>Emploi du temps</th>
                                    <th>Spécialité</th>
                                    <th>Tient la classe depuis</th>
                                    <th>Contacts</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach($teachers as $k => $t)
                                        <tr class="py-3">
                                            <td class="text-center border-right">{{ $loop->iteration }}</td>
                                            <td class="text-left px-2"> 
                                                <a style="color: inherit !important;" class="m-0 p-0 py-1" href="{{route('user_profil', ['id' => $t->user->id])}}">
                                                    <span class="d-flex justify-content-between">
                                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                                            {{ $t->getFormatedName() }}
                                                        </span>
                                                        <span>
                                                            @if($t->hasClasses())
                                                                <span class="d-flex justify-content-between">
                                                                    <span class="d-flex justify-content-start small text-orange">
                                                                        @foreach($t->getTeachersCurrentClasses() as $c)
                                                                            @php
                                                                                $cl = $c->getNumericName();
                                                                            @endphp
                                                                            <small style="color: inherit !important;" class=" py-1 px-2 mr-1 my-1">
                                                                                {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                                                            </small>
                                                                        @endforeach
                                                                    </span>
                                                                </span>
                                                            @else
                                                                <small class="text-orange">Aucune classe assignée!</small>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-left pl-1"> 
                                                
                                            </td>
                                            <td class="text-center"> {{ $t->contacts }}</td>
                                            <td class="text-center"> {{ $t->speciality() ? $t->speciality()->name : 'Non définie' }}</td>
                                            
                                            <td class="text-center"> 
                                                <span class="row w-100 m-0 p-0">
                                                    
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>        