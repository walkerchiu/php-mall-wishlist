<?php

namespace WalkerChiu\MallWishlist\Models\Entities;

use Illuminate\Database\Eloquent\UuidModel;

class Item extends UuidModel
{
    protected $fillable = [
        'user_id',
        'stock_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.mall-wishlist.items');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('wk-core.class.user'), 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(config('wk-core.class.mall-shelf.stock'), 'stock_id', 'id');
    }
}
