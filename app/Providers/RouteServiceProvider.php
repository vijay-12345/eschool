<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\School, App\Teacher;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        Route::middleware('web')
            ->namespace('App\Http\Controllers\BackEnd')
            ->prefix('admin')
            ->group(base_path('routes/backend.php'));
        

        $schoollist= School::pluck('name');
        foreach ($schoollist as $key => $value) {
            $value=strtolower(str_replace(" ", "_", $value));
            Route::middleware('web')
                ->namespace('App\Http\Controllers\BackEnd')
                ->prefix("school/$value")
                ->group(base_path('routes/backend.php'));
        }

        $teacherlist= Teacher::pluck('name');
        foreach ($teacherlist as $key => $value) {
            $value=strtolower(str_replace(" ", "_", $value));
            Route::middleware('web')
                ->namespace('App\Http\Controllers\BackEnd')
                ->prefix("teacher/$value")
                ->group(base_path('routes/backend.php'));
        }  
         Route::middleware('web')
                ->namespace('App\Http\Controllers\BackEnd')
                ->prefix("school")
                ->group(base_path('routes/backend.php'));

        Route::middleware('web')
            ->namespace('App\Http\Controllers\FrontEnd')
            ->group(base_path('routes/frontend.php'));

        Route::middleware('web')
                ->namespace('App\Http\Controllers\BackEnd')
                ->prefix("teacher")
                ->group(base_path('routes/backend.php'));    
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api/v2/')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
