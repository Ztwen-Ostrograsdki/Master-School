<div>
    <section class="content">
        <div class="container-fluid">
            <div class="w-100">
                @livewire('admin-widgets-component')
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            @livewire('admin-direct-chat-component')
                        </div>
                        <div class="col-md-6">
                            @livewire('admin-users-listing-component')
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @livewire('admin-activities-stats-component')
                </div>
                <div class="col-md-4">
                    @livewire('admin-lasts-payments-component')
                </div>
            </div>
        </div>
      </section>
</div>
