<div>
    @if(auth()->user()->teacher || auth()->user()->isAdminAs('master') || auth()->user()->isAuthorizedAdmin())
        
        @livewire('insert-pupil-related-mark')
        
        @livewire('marks-settings-modal')
        @livewire('manage-classe-modalities')
        @livewire('add-new-teacher')
        @livewire('insert-time-plan')
        @livewire('classe-marks-deleter-component')
        @livewire('mark-manager')
        @livewire('presence-late-modal')
        @livewire('marks-null-actions-confirmation')
        @livewire('reset-absences-and-lates-confirmation')
        @livewire('insert-pupil-marks')
        @livewire('insert-classe-pupils-marks-together')
        @livewire('classe-marks-convertion-confirmation')

    @endif
    
    @livewire('parent-follow-new-pupil')
    
    @livewire('profil-image-editor')
    
    
    
</div>
