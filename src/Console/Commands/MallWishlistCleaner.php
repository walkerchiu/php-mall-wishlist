<?php

namespace WalkerChiu\MallWishlist\Console\Commands;

use WalkerChiu\Core\Console\Commands\Cleaner;

class MallWishlistCleaner extends Cleaner
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MallWishlistCleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate tables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::clean('mall-wishlist');
    }
}
