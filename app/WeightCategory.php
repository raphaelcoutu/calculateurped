<?php

namespace App;

class WeightCategory
{
    public static function get($weight)
    {
        if ($weight > 0 && $weight <= 5) {
            return 1;
        } else if ($weight > 5 && $weight <= 10) {
            return 2;
        } else if ($weight > 10 && $weight <= 15) {
            return 3;
        } else if ($weight > 15 && $weight < 35) {
            return 4;
        } else if ($weight >= 35) {
            return 5;
        } else {
            return 0;
        }
    }
}