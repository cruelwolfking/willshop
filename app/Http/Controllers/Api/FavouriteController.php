<?php

namespace App\Http\Controllers\Api;

use App\Favourite;
use App\Product;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class FavouriteController extends Controller
{
    /**
     * @var Favourite
     */
    private $favourite;

    /**
     * @var Product
     */
    private $product;

    /**
     * FavouriteController constructor.
     *
     * @param Favourite $favourite
     * @param Product   $product
     */
    public function __construct(Favourite $favourite, Product $product)
    {
        $this->favourite = $favourite;
        $this->product = $product;
    }

    /**
     * 加入购物车
     *
     * @param $productId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add($productId)
    {
        $data['user_id'] = 1;
        $data['product_id'] = $productId;

        $this->favourite->create($data);

        return response()->json(['info' => '添加成功']);
    }

    /**
     * 删除
     *
     * @param $favouriteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($productId)
    {
        $data['user_id'] = 1;
        $data['product_id'] = $productId;

        $this->favourite->where($data)->delete();

        return response()->json(['info' => '删除成功']);
    }

    /**
     * 切换收藏状态
     *
     * @param $productId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($productId)
    {
        $data['user_id'] = 1;
        $data['product_id'] = $productId;

        if ($this->favourite->where($data)->first()) {
            return $this->delete($productId);
        } else {
            return $this->add($productId);
        }
    }

    /**
     * 列出收藏
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function lists()
    {
        return $this->favourite->all();
    }
}
