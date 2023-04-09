<?php

namespace Almooradi\FilamentEcommerce\Models\Product;

use Almooradi\FilamentEcommerce\Constants\Gender;
use Almooradi\FilamentEcommerce\Constants\ProductStatus;
use Almooradi\FilamentEcommerce\Constants\SortingOption;
use Almooradi\FilamentEcommerce\Models\Category;
use Almooradi\FilamentEcommerce\Models\Variation\Variation;
use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, HasShowIn, SoftDeletes;

    protected $table = 'shop_products';

    protected $casts = [
        'show_in' => 'array',
        'media_files' => 'array',
    ];

    protected $guarded = [];

    protected $appends = ['discount_price', 'is_purchasable', 'thumbnail_image'];

    protected $contentSearchColumns = [
        [
            'name' => 'title',
            'weights' => [
                'includes' => 1000,
                'whole_word' => 100,
                'start' => 50,
                'end' => 10,
            ]
        ],
        [
            'name' => 'short_description',
            'weights' => [
                'includes' => 500,
                'whole_word' => 50,
                'start' => 25,
                'end' => 5,
            ]
        ],
        [
            'name' => 'long_description',
            'weights' => [
                'includes' => 250,
                'whole_word' => 25,
                'start' => 10,
                'end' => 2,
            ]
        ],
    ];

    const DISCOUNT_PRICE_SQL = "
        IF (discount_type = 'fixed',
            price - discount_amount,
            IF (discount_type = 'percentage',
                price - price * discount_amount / 100,
                null
            )
        )
    ";

    const FINAL_PRICE_SQL = "IF (" . self::DISCOUNT_PRICE_SQL . " >= 0, " . self::DISCOUNT_PRICE_SQL . ", price)";


    /**
     * Categories relation
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_products_categories');
    }

    /**
     * Variations relation
     *
     * @return BelongsToMany
     */
    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(Variation::class, 'shop_products_variations', 'product_id', 'variation_id');
    }

    /**
     * Parent product relation
     *
     * @return BelongsTo
     */
    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id')->where('parent_product_id', null);
    }

    /**
     * Get "discount_price" attribute
     *
     * @return string|null
     */
    public function getDiscountPriceAttribute(): string|null
    {
        // ! NOTE: These calculations are done also in "scopeOrderByPrice()" method, so anything is changed here, it must also be changed in this methos "scopeOrderByPrice()"

        $discountPrice = null;
        if ($this->discount_type == 'fixed') {
            $discountPrice = number_format($this->price - $this->discount_amount, 2);
        } else if ($this->discount_type == 'percentage') {
            $discountPrice = number_format($this->price - $this->price * $this->discount_amount / 100, 2);
        }

        return $discountPrice;
    }

    /**
     * Get "is_purchasable" attribute
     *
     * @return boolean
     */
    public function getIsPurchasableAttribute(): bool
    {
        // ! NOTE: This consept is used also in "scopeWherePurchasable()" method, so anything is changed here, it must also be changed in this methos "scopeWherePurchasable()"

        $is_purchasable = false;
        if ($this->price !== null && $this->price >= 0) {
            $is_purchasable = true;
        }

        return $is_purchasable;
    }

    /**
     * Scope purchasable products
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWherePurchasable(Builder $query): void
    {
        // ! NOTE: This consept is used also in "getIsPurchasableAttribute()" method, so anything is changed here, it must also be changed in this methos "getIsPurchasableAttribute()"

        $query->where('price', '>=', 0)
            ->where('price', '!=', null);
    }

    /**
     * Scope products based on the "gender"
     *
     * @param Builder $query
     * @param null|int $gender
     * @return void
     */
    public function scopeWhereGender(Builder $query, null|int $gender): void
    {
        if (isset(Gender::ITEM_OPTIONS[$gender])) {
            $query->where('gender', $gender);
        }
    }

    /**
     * Get gender as a text instead of ID
     *
     * @return string
     */
    public function getGenderTextAttribute(): string
    {
        return Gender::ITEM_OPTIONS[$this->gender] ?? '';
    }

    /**
     * Get "thumbnail image" attribute
     *
     * @return null|string
     */
    public function getThumbnailImageAttribute(): null|string
    {
        $firstImagePath = null;

        $supported_image = ['gif', 'jpg', 'jpeg', 'png'];
        foreach ($this->media_files ?? [] as $filePath) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if (in_array($ext, $supported_image)) {
                $firstImagePath = $filePath;
                break;
            }
        }

        $thumbnailImagePath = $firstImagePath ? Storage::url($firstImagePath) : asset('assets\packages\vendor\filament-ecommerce\images\products\product-default-thumbnail-500x500.png');

        return str_replace('\\', '/', $thumbnailImagePath);
    }

    /**
     * Check if product is published or not
     *
     * @return boolean
     */
    public function isPublished(): bool
    {
        return $this->status == ProductStatus::PUBLISHED;
    }

    /**
     * Scope data where status is "published"
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeWherePublished(Builder $query): void
    {
        $query->where('status', ProductStatus::PUBLISHED);
    }

    /**
     * Scope data where status is "published"
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeWhereInContent(Builder $query, $value): void
    {
        $query->where(function ($query) use ($value) {
            foreach ($this->contentSearchColumns as $searchColumn) {
                $query->orWhere($searchColumn['name'], 'LIKE', '%' . $value . '%');
            }
        });
    }

    /**
     * Order products by relevant to a search value 
     *
     * @param Builder $query
     * @param string $searchValue
     * @return void
     */
    public function scopeOrderByRelevance(Builder $query, string $searchValue): void
    {
        function addPlusAtTheEnd(string $string): string
        {
            $string = trim($string);
            $string = rtrim($string, '+');

            return $string . '+';
        }

        $relevanceRaw = '';
        foreach ($this->contentSearchColumns as $searchColumn) {
            if (strlen($relevanceRaw) > 0) {
                $relevanceRaw = addPlusAtTheEnd($relevanceRaw);
            }

            $weights = $searchColumn['weights'] ?? [];
            if (isset($weights['includes'])) {
                $relevanceRaw = addPlusAtTheEnd($relevanceRaw);

                $relevanceRaw .= "(CASE WHEN " . $searchColumn['name'] . " LIKE '%" . $searchValue . "%' THEN " . $weights['includes'] . " ELSE 0 END)";
            }
            if (isset($weights['whole_word'])) {
                $relevanceRaw = addPlusAtTheEnd($relevanceRaw);

                $relevanceRaw .= "(CASE WHEN " . $searchColumn['name'] . " LIKE '% " . $searchValue . " %' THEN " . $weights['whole_word'] . " ELSE 0 END)";
            }
            if (isset($weights['start'])) {
                $relevanceRaw = addPlusAtTheEnd($relevanceRaw);

                $relevanceRaw .= "(CASE WHEN " . $searchColumn['name'] . " LIKE '" . $searchValue . "%' THEN " . $weights['start'] . " ELSE 0 END)";
            }
            if (isset($weights['end'])) {
                $relevanceRaw = addPlusAtTheEnd($relevanceRaw);

                $relevanceRaw .= "(CASE WHEN " . $searchColumn['name'] . " LIKE '%" . $searchValue . "' THEN " . $weights['end'] . " ELSE 0 END)";
            }
        }

        $query->when(strlen($relevanceRaw) > 0, function ($query) use ($relevanceRaw) {
            $query->select($query->getQuery()->columns ?? '*')
                ->addSelect(DB::raw("(" . $relevanceRaw . ") AS relevance")) // TODO: Check if this can make an SQL injection
                ->orderBy('relevance', 'desc');
        });
    }

    /**
     * Get max price (regular & discount) from a collection of products
     *
     * @param Collection $products
     * @return float
     */
    public function getMaxPrice(Collection $products): float
    {
        $maxRegularPrice =  $products->whereNull('discount_price')->max('price') ?? 0;
        $maxDiscountPrice =  $products->whereNotNull('discount_price')->max('discount_price') ?? 0;

        return max($maxRegularPrice, $maxDiscountPrice);
    }

    /**
     * Get min price (regular & discount) from a collection of products
     *
     * @param Collection $products
     * @return float
     */
    public function getMinPrice(Collection $products): float
    {
        // ! We used "PHP_INT_MAX" instead of "Inf (Infinity)" because the last returns this error "Inf and NaN cannot be JSON encoded"

        $minRegularPrice =  $products->whereNull('discount_price')->min('price') ?? PHP_INT_MAX;
        $minDiscountPrice =  $products->whereNotNull('discount_price')->min('discount_price') ?? PHP_INT_MAX;

        $min = min($minRegularPrice, $minDiscountPrice);

        return $min == PHP_INT_MAX ? 0 : $min;
    }

    /**
     * Add "final_price" custom column select SQL
     *
     * @param Builder $query
     * @return void
     */
    public function scopeAddFinalPriceSelectSql(Builder $query): void
    {
        $isFinalPriceExists = false;
        foreach ($query->getQuery()->columns ?? [] as $column) {
            $isFinalPriceExists = strpos($column, 'final_price') > 0;

            if ($isFinalPriceExists) {
                break;
            }
        }

        if ($isFinalPriceExists) {
            return;
        }

        $query->select($query->getQuery()->columns ?? '*')
            ->addSelect(DB::raw("CAST(" . self::FINAL_PRICE_SQL . "AS DECIMAL(10, 6)) AS final_price"));
    }

    /**
     * Order products by price
     *
     * @param Builder $query
     * @param string $order
     * @return void
     */
    public function scopeOrderByPrice(Builder $query, $order): void
    {
        $query->addFinalPriceSelectSql()
            ->orderBy('final_price', $order);
    }

    /**
     * Get products by price
     *
     * @param Builder $query
     * @param int|float|string $price
     * @param string $operator
     * @return void
     */
    public function scopeWherePrice(Builder $query, int|float|string $price, string $operator = '='): void
    {
        $query->addFinalPriceSelectSql()
            ->having('final_price', $operator, $price);
    }

    /**
     * Get products between prices
     *
     * @param Builder $query
     * @param null|integer|float|string $priceFrom
     * @param null|integer|float|string $priceTo
     * @return void
     */
    public function scopeWherePriceBetween(Builder $query, null|int|float|string $priceFrom, null|int|float|string $priceTo): void
    {
        $query->when($priceFrom !== null && $priceFrom >= 0, fn ($query) => $query->wherePrice($priceFrom, '>='))
            ->when($priceTo !== null && $priceTo >= 0, fn ($query) => $query->wherePrice($priceTo, '<='));
    }

    /**
     * Scope products by categories
     *
     * @param Builder $query
     * @param array $categoriesIds
     * @return void
     */
    public function scopeWhereCategories(Builder $query, array $categoriesIds): void
    {
        $categoriesIds = array_filter($categoriesIds, "is_numeric");

        $query->when(count($categoriesIds ?? []) > 0, function ($query) use ($categoriesIds) {
            $query->whereHas('categories', function ($query) use ($categoriesIds) {
                $query->whereIn('category_id', $categoriesIds)
                    ->orWhereHas('parent', fn ($query) => $query->whereIn('id', $categoriesIds));
            });
        });
    }

    /**
     * Sort products by filter value
     *
     * @param Builder $query
     * @param string $sorting
     * @return void
     */
    public function scopeSortingBy(Builder $query, $sorting, $addtionalData = []): void
    {
        switch ($sorting) {
            case SortingOption::RELEVANCE: {
                    if (strlen($addtionalData['search_value'] ?? '') > 0) {
                        $query->orderByRelevance($addtionalData['search_value']);
                    }
                    break;
                }
            case SortingOption::PRICE_ASC: {
                    $query->orderByPrice('asc');
                    break;
                }
            case SortingOption::PRICE_DESC: {
                    $query->orderByPrice('desc');
                    break;
                }
            case SortingOption::CREATED_AT_ASC: {
                    $query->orderBy('id', 'asc');
                    break;
                }
            case SortingOption::CREATED_AT_DESC: {
                    $query->orderBy('id', 'desc');
                    break;
                }
                // case SortingOption::POPULARITY_DESC: {
                //         $query->orderBy('is_popular', 'desc')->orderBy('created_at', 'desc');
                //         break;
                //     }
                // case SortingOption::RATING_DESC: {
                //         $query->orderBy('reviews_average', 'desc');
                //         break;
                //     }
        }
    }
}
