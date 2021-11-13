<?php declare(strict_types=1);

namespace Sms\SDK;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Laravel\Lumen\Application as LumenApplication;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'sms');

        $this->app->singleton('sms.client', function ($app) {
            $options = $app['config']->get('sms');

            if (!isset($options['api_url'])) {
                throw new InvalidArgumentException('Not found api_url config');
            }

            if (!isset($options['oauth']['url'])) {
                throw new InvalidArgumentException('Not found oauth.url config');
            }

            if (!isset($options['oauth']['client_id'])) {
                throw new InvalidArgumentException('Not found oauth.client_id config');
            }

            if (!isset($options['oauth']['client_secret'])) {
                throw new InvalidArgumentException('Not found oauth.client_secret config');
            }

            return new SmsClient($options['api_url']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('sms.php')], 'sms');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sms');
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/sms.php';
    }
}
