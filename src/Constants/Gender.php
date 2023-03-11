<?php

namespace Almooradi\FilamentEcommerce\Constants;

class Gender
{
    // ! We have only two genders
    const MALE = 1;
    const FEMALE = 2;

    const UNISEX = 3;
    const ALL = 4;

    const GENDERS = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    const FILTER_OPTIONS = [
        self::ALL => 'All',
        self::UNISEX => 'Unisex',
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    const DEFAULT_FILTER_OPTION = self::ALL;

    const ITEM_OPTIONS = [
        self::UNISEX => 'Unisex',
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];
}
