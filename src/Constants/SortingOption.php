<?php

namespace Almooradi\FilamentEcommerce\Constants;

class SortingOption
{
    const RELEVANCE = 1;
    const PRICE_ASC = 2;
    const PRICE_DESC = 3;
    const CREATED_AT_ASC = 4;
    const CREATED_AT_DESC = 5;
    const TITLE_ASC = 6;
    const TITLE_DESC = 7;
    const RATING_ASC = 8;
    const RATING_DESC = 9;
    const POPULARITY_ASC = 10;
    const POPULARITY_DESC = 11;
    
    const DEFAULT = self::RELEVANCE;

    const ALL = [
        SELF::RELEVANCE => 'Relevance',
        SELF::PRICE_ASC => 'Price | Low To High',
        SELF::PRICE_DESC => 'Price | High To Low',
        SELF::CREATED_AT_ASC => 'Oldest',
        SELF::CREATED_AT_DESC => 'Newest',
        SELF::TITLE_ASC => 'Title | A-Z',
        SELF::TITLE_DESC => 'Title | Z-A',
        SELF::RATING_ASC => 'Rating | Low To High',
        SELF::RATING_DESC => 'Rating | High To Low',
        SELF::POPULARITY_ASC => 'Non Popular',
        SELF::POPULARITY_DESC => 'Most Popular',
    ];
}
