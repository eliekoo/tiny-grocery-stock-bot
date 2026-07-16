<?php

namespace App\Services;

use App\Models\ProductAlias;
use App\Models\Variant;

class ProductSearchService
{
    /**
     * Find a variant by alias or name.
     */
    public function search(string $keyword)
    {
        $keyword = trim(mb_strtolower($keyword));

        // 1. Exact alias match
        $alias = ProductAlias::query()
            ->whereRaw(
                'LOWER(alias) LIKE ?',
                ['%' . $keyword . '%']
            )
            ->with('variant')
            ->orderByDesc('priority')
            ->first();

        if ($alias) {
            return $alias->variant;
        }

        // 2. Exact variant name
        $variant = Variant::query()
            ->whereRaw(
                'LOWER(name) LIKE ?',
                ['%' . $keyword . '%']
            )
            ->first();

        if ($variant) {
            return $variant;
        }
    }
}
