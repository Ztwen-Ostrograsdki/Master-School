<div>
    @if(auth()->user()->teacher || auth()->user()->isAdminAs('master') || auth()->user()->isAuthorizedAdmin())
        @livewire('insert-pupil-marks')
        @livewire('insert-pupil-related-mark')
        @livewire('mark-manager')
        @livewire('marks-settings-modal')
        @livewire('manage-classe-modalities')
    @endif
    @livewire('profil-image-editor')
    
</div>
