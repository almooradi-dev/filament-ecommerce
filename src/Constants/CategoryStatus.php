<?php

namespace Almooradi\FilamentEcommerce\Constants;

class CategoryStatus
{
    const INACTIVE = 1;
    const ACTIVE = 2;
    const PENDING = 3;

    const ALL = [
        self::INACTIVE => 'Inactive',
        self::ACTIVE => 'Active',
        self::PENDING => 'Pending',
    ];
}
