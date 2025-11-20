<?php
namespace DbHealth;

use Illuminate\Support\ServiceProvider;
use DbHealth\Commands\ExplainSampleCommand;
use DbHealth\Commands\HealthReportCommand;

class DbHealthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ExplainSampleCommand::class,
                HealthReportCommand::class,
            ]);
            $this->publishes([
                __DIR__ . '/../config/db-health.php' => config_path('db-health.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/db-health.php', 'db-health');
        $this->app->singleton('dbhealth.reporter', function($app){
            return new Report\Reporter($app['db'], $app['log']);
        });
    }
}
