<?php

namespace Almooradi\FilamentEcommerce\Models;

use Almooradi\FilamentEcommerce\Traits\HasShowIn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

	/**
	 * Get "thumbnail image" attribute
	 *
	 * @return null|string
	 */
	public function getThumbnailImageAttribute(): null|string
	{
		$firstImagePath = null;

		$supported_image = ['gif', 'jpg', 'jpeg', 'png'];
		$filePath = $this->image ?? '';
		$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

		if (in_array($ext, $supported_image) && Storage::exists('public/' . $filePath)) {
			$firstImagePath = $filePath;
		}

		$thumbnailImagePath = $firstImagePath ? Storage::url($firstImagePath) : asset('assets\packages\vendor\filament-ecommerce\images\categories\category-default-thumbnail-500x500.png');

		return str_replace('\\', '/', $thumbnailImagePath);
	}
}
