<?php

namespace WalkerChiu\MallWishlist\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\MallShelf\Models\Services\StockService;

class ItemRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-wishlist.item'));
    }

    /**
     * @param Array $data
     * @param Int   $page
     * @param Int   $nums per page
     * @return Array
     */
    public function list(String $code, Array $data, $page = null, $nums = null)
    {
        $this->assertForPagination($page, $nums);

        $entity = $this->entity;

        $data = array_map('trim', $data);
        $records = $entity->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['user_id']), function ($query) use ($data) {
                                            return $query->where('user_id', $data['user_id']);
                                        })
                                        ->unless(empty($data['stock_id']), function ($query) use ($data) {
                                            return $query->where('stock_id', $data['stock_id']);
                                        });
                            })
                            ->orderBy('updated_at', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });
        $list = [];
        foreach ($records as $record) {
            array_push($list, $this->show($record, $code));
        }

        return $list;
    }

    /**
     * @param Item   $entity
     * @param String $code
     * @return Array
     */
    public function show($entity, String $code)
    {
        $data = [
            'id'         => $entity->id,
            'stock_id'   => $entity->stock_id,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'stock'      => []
        ];

        if ( config('wk-mall-wishlist.onoff.mall-shelf') && !empty(config('wk-core.class.mall-shelf.stock')) ) {
            $service = \WalkerChiu\MallShelf\Models\Services\StockService();
            $data['stock'] = $service->showForItem($entity->stock, $code);
        }

        return $data;
    }

    /**
     * @param String $code
     * @param String $user_id
     * @param String $stock_id
     * @return Array
     */
    public function add(String $code, String $user_id, String $stock_id)
    {
        $record = $this->where('user_id', '=', $user_id)
                       ->where('stock_id', '=', $stock_id)
                       ->first();

        if (empty($record))
            $record = $this->save([
                'user_id'  => $user_id,
                'stock_id' => $stock_id
            ]);
        else
            $record->touch();

        return $this->show($record, $code);
    }

    /**
     * @param String $user_id
     * @param Array  $stock_id
     * @return Boolean
     */
    public function remove(String $user_id, Array $stock_id)
    {
        return $this->where('user_id', '=', $user_id)
                    ->whereIn('stock_id', $stock_id)
                    ->delete();
    }
}
