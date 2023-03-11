<?php

namespace Almooradi\FilamentEcommerce\Models;

use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, HasShowIn, SoftDeletes;

    protected $table = 'shop_categories';

    protected $casts = [
        'show_in' => 'array',
    ];

    protected $guarded = [];


    /**
     * Scope parent categories
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeWhereIsParent($query)
    {
        $query->where(function ($query) {
            $query->where('parent_category_id', 0)
                ->orWhere('parent_category_id', null);
        });
    }

    /**
     * Children categories relation
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    /**
     * Parent category relation
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }
}
