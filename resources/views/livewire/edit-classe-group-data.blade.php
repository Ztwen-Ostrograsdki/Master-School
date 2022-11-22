<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Edition de promotion'" :width="6" :icon="'fa fa-filter'" :modalName="'editClasseGroupDataModal'" :modalBodyTitle="'Edition de promotion de classes'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="col-12 m-0 p-0 row  mx-auto justify-center">
                @if($classe_group)
                <blockquote class="text-info col-11 m-0 my-2">
                    <span class="fa fa-edit"></span>
                    Edition de la promotion : 
                    <span class="text-warning">
                        {{$classe_group->name}}
                    </span>
                </blockquote>
                @endif
            </div>
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex row">
                    <x-z-input :type="'text'" :error="$errors->first('name')" :modelName="'name'" :labelTitle="'Le Nom de la promotion'" ></x-z-input>
                </div>
                <div class="d-flex row my-1">
                    <x-z-input :type="'text'" :error="$errors->first('category')" :modelName="'category'" :placeholder="'Renseignez la catégorie de cette promotion'" :labelTitle="'La catégorie de cette promotion'" ></x-z-input>
                </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Mettre à jour</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    