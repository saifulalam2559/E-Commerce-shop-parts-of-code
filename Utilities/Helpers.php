<?php

class Helper
{
    public static function userDefaultImage()
    {
        return asset("frontend/images/default.png");
    }

    public static function minPrice()
    {
        return floor(App\Models\Product::min("offer_price"));
    }

    public static function maxPrice()
    {
        return floor(App\Models\Product::max("offer_price"));
    }

    public static function minDiscount()
    {
        return floor(App\Models\Product::min("discount"));
    }

    public static function maxDiscount()
    {
        return floor(App\Models\Product::max("discount"));
    }
}
