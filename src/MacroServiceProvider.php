<?php

namespace mav3rick177\RapidPagination;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * Class MacroServiceProvider
 */
class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register "rapid-pagination" macros.
     */
    public function register()
    {
        QueryBuilder::macro('rapid_pagination', function () {
            /* @var \Illuminate\Database\Query\Builder $this */
            return Paginator::create($this);
        });
        EloquentBuilder::macro('rapid_pagination', function () {
            /* @var \Illuminate\Database\Eloquent\Builder $this */
            return Paginator::create($this);
        });
        Relation::macro('rapid_pagination', function () {
            /* @var \Illuminate\Database\Eloquent\Relations\Relation $this */
            return Paginator::create($this);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapid-pagination');
        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/rapid-pagination'),
        ], 'rapid-pagination');

        if (\File::exists(__DIR__ . '\Helpers\helpers.php')) {
            require __DIR__ . '\Helpers\helpers.php';
        }
    }
}
