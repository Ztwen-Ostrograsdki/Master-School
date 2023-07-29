<?php

namespace App\Providers;

use App\Events\NewProductCreatedEvent;
use App\Events\PaymentSystemEvent;
use App\Listeners\NewProductCreatedListener;
use App\Listeners\PaymentSystemListener;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Observers\ClasseObserver;
use App\Observers\MarkObserver;
use App\Observers\PupilObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NewProductCreatedEvent::class => [
            NewProductCreatedListener::class,
        ],
        PaymentSystemEvent::class => [
            PaymentSystemListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Pupil::observe(PupilObserver::class);

        Mark::observe(MarkObserver::class);

        Classe::observe(ClasseObserver::class);

    }

}
