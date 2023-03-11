<?php

namespace Almooradi\FilamentEcommerce\Constants;

class ProductStatus
{
    const DRAFT = 1;
    const PENDING = 2;
    const PUBLISHED = 3;
    const REJECTED = 4;

    const ALL = [
        self::DRAFT => 'Draft',
        self::PENDING => 'Pending',
        self::PUBLISHED => 'Published',
        self::REJECTED => 'Rejected',
    ];
}
