<div>
    @if(auth()->user()->teacher || auth()->user()->isAdminAs('master') || auth()->user()->isAuthorizedAdmin())
        
        @livewire('insert-pupil-related-mark')
        
        @livewire('marks-settings-modal')
        @livewire('manage-classe-modalities')
        @livewire('add-new-teacher')
        @livewire('insert-time-plan')
        @livewire('classe-marks-deleter-component')
    @endif
    @livewire('mark-manager')
    @livewire('parent-follow-new-pupil')
    @livewire('insert-pupil-marks')
    @livewire('insert-classe-pupils-marks-together')
    @livewire('profil-image-editor')
    @livewire('presence-late-modal')
    
    
</div>
