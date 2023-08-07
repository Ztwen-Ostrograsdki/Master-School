<div>
    @if(auth()->user()->teacher || auth()->user()->isAdminAs('master') || auth()->user()->isAuthorizedAdmin())
        
        @livewire('insert-pupil-related-mark')
        
        @livewire('marks-settings-modal')
        @livewire('manage-classe-modalities')
        @livewire('add-new-teacher')
        @livewire('insert-time-plan')
    @endif
    @livewire('mark-manager')
    @livewire('parent-follow-new-pupil')
    @livewire('insert-pupil-marks')
    @livewire('profil-image-editor')
    
    
</div>
