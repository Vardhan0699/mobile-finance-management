<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Swift_SmtpTransport;


class AppServiceProvider extends ServiceProvider
{
  /**
     * Register any application services.
     */
  public function register()
  {
    $this->app->singleton(\App\Services\AadhaarService::class);
  }

  /**
     * Bootstrap any application services.
     */
  public function boot() : void
  {
    Mail::extend('smtp-insecure', function () {
      $transport = new Swift_SmtpTransport(
        env('MAIL_HOST'),
        env('MAIL_PORT'),
        env('MAIL_ENCRYPTION')
      );

      $transport->setUsername(env('MAIL_USERNAME'));
      $transport->setPassword(env('MAIL_PASSWORD'));

      $transport->setStreamOptions([
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true,
        ],
      ]);

      return new \Swift_Mailer($transport);
    });
  }


}
