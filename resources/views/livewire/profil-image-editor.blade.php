<div>
    <x-z-galery-manager-component 
    :modalName="'updateProfilImageModal'"
    :modalHeaderTitle="'Edition de la photo de profil'"
    :modelName="'profil_image'"
    :theModel="$profil_image"
    :labelTitle="'Veuillez selectionner une image'"
    :submitMethodName="'updateImage'"
    :error="$errors->first('profil_image')"
    >
    </x-z-galery-manager-component>
</div>