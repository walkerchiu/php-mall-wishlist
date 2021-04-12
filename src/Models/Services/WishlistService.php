<?php

namespace WalkerChiu\MallWishlist\Models\Services;

use Illuminate\Support\Facades\App;

class WishlistService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.mall-wishlist.itemRepository'));
    }

    /**
     * @param  String $user_id
     * @return Boolean
     */
    public function clear(String $user_id)
    {
        return $this->repository->where('user_id', '=', $user_id)
                                ->delete();
    }
}
