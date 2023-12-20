<?php

namespace QuetzalStudio\YUKK\MoneyTransfer;

use Illuminate\Support\ServiceProvider;

class MoneyTransferServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/yukk_money_transfer.php', 'yukk_money_transfer'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
