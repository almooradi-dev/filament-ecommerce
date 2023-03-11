<?php

namespace Almooradi\FilamentEcommerce\Traits;

use Illuminate\Http\Request;

trait HasShowIn {
    /**
     * Scope resources with respect to the "show_in" field
     *
     * @param QueryBuilder $query
     * @param string|array $showIn
     * @return void
     */
    public function scopeWhereShowIn($query, string|array $showIn): void
    {
        $showIn = is_array($showIn) ? $showIn : [$showIn];

        $query->where(function ($query) use ($showIn) {
            foreach ($showIn as $showInItem) {
                $query->orWhere('show_in', 'like', '%"' . $showInItem . '"%');
            }
        });
    }
}