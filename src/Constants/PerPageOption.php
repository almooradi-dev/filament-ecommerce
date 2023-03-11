<?php

namespace Almooradi\FilamentEcommerce\Constants;

class PerPageOption
{
    const TEN = 10;
    const TWENTY = 20;
    const FIFTY = 50;

    const DEFAULT = self::TEN;
    
    const ALL = [
        SELF::TEN => '10',
        SELF::TWENTY => '20',
        SELF::FIFTY => '50',
    ];
}
