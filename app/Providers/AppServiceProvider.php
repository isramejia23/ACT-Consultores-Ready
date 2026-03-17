<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\App;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        App::setLocale('es');
        
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8'); // Para sistemas UNIX/Linux
        setlocale(LC_TIME, 'Spanish'); 
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Blade::directive('formatoFecha', function ($expression) {
            return "<?php echo Carbon\\Carbon::parse($expression)->format('d/m/Y'); ?>";
        });

    }
}
