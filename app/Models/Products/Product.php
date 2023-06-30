<?php

namespace App\Models\Products;

use App\Http\Traits\CustomModelLogic;
use App\Models\Catalogs\CatBrand;
use App\Models\Catalogs\CatProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, CustomModelLogic;

    protected $appends = ['hash_id'];
    protected $hidden = ['id'];
    protected $fillable = [
        'barcode',
        'name',
        'cat_brand_id',
        'cat_product_type_id',
        'tag_id',
        'description',
        'batch',
        'expiration',
        'purchase_price',
        'sale_price',
        'discount',
        'gain',
        'stock',
        'min_stock',
        'ieps',
        'iva',
        'compound',
        'pharmaceutical_form',
        'concentration',
        'fraction',
        'antibiotic',
        'therapeutic_indication',
        'comments',
    ];

    public function getData($params)
    {
        Product::$withoutAppends = false;
        return Product::select(
            [
                'id',
                'barcode',
                'name',
                'cat_brand_id',
                'cat_product_type_id',
                'tag_id',
                'description',
                'batch',
                'expiration',
                'purchase_price',
                'sale_price',
                'discount',
                'gain',
                'stock',
                'min_stock',
                'ieps',
                'iva',
                'compound',
                'pharmaceutical_form',
                'concentration',
                'fraction',
                'antibiotic',
                'therapeutic_indication',
                'comments',
            ]
        )->with([
            'brand:id,name',
            'type:id,name',
            'tag:id,name',
            'discount'
        ])->paginate($params['rowsPerPage']);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CatBrand::class, 'cat_brand_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CatProductType::class, 'cat_product_type_id');
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function discount(): HasOne
    {
        return $this->hasOne(ProductDiscount::class, 'product_id');
    }
}
