<?php
/**
 * AutoCare - Intelligent Vehicle Image Matcher
 * Maps vehicle company, model, fuel, and category to real transparent PNG assets.
 */

if (!function_exists('get_vehicle_image')) {
    function get_vehicle_image($company, $model = '', $fuel = '')
    {
        $c = strtolower(trim($company ?? ''));
        $m = strtolower(trim($model ?? ''));
        $f = strtolower(trim($fuel ?? ''));
        $combined = $c . ' ' . $m;

        // 1. Two-wheelers & EV Scooters
        if (
            strpos($combined, 'ather') !== false ||
            strpos($combined, 'ola') !== false ||
            strpos($combined, 'chetak') !== false ||
            strpos($combined, 'iqube') !== false ||
            strpos($combined, 'bounce') !== false ||
            strpos($combined, 'hero electric') !== false ||
            strpos($combined, 'ampere') !== false ||
            strpos($combined, 'okinawa') !== false ||
            strpos($combined, 'pure ev') !== false ||
            strpos($combined, 'simple energy') !== false ||
            strpos($combined, '450') !== false ||
            strpos($combined, 'rizta') !== false ||
            strpos($combined, 's1') !== false ||
            strpos($combined, 'royal enfield') !== false ||
            strpos($combined, 'yamaha') !== false ||
            strpos($combined, 'tvs') !== false ||
            strpos($combined, 'bajaj') !== false ||
            strpos($combined, 'hero') !== false ||
            strpos($combined, 'ktm') !== false ||
            strpos($combined, 'pulsar') !== false ||
            strpos($combined, 'bullet') !== false
        ) {
            return 'car3.png';
        }

        // 2. Off-road SUVs & Thar
        if (
            strpos($m, 'thar') !== false ||
            strpos($m, 'scorpio') !== false ||
            strpos($m, 'bolero') !== false ||
            strpos($m, 'jimny') !== false
        ) {
            return 'thar.png';
        }

        // 3. Fortuner & Full-size SUVs
        if (
            strpos($m, 'fortuner') !== false ||
            strpos($m, 'innova') !== false ||
            strpos($m, 'hycross') !== false ||
            strpos($m, 'crysta') !== false
        ) {
            return 'fortuner.png';
        }

        // 4. Tata Nexon & Compact SUVs
        if (
            strpos($c, 'tata') !== false ||
            strpos($m, 'nexon') !== false ||
            strpos($m, 'punch') !== false ||
            strpos($m, 'harrier') !== false ||
            strpos($m, 'safari') !== false ||
            strpos($m, 'curvv') !== false
        ) {
            return 'nexon.png';
        }

        // 5. Sedans
        if (
            strpos($c, 'honda') !== false ||
            strpos($m, 'city') !== false ||
            strpos($m, 'amaze') !== false ||
            strpos($m, 'elevate') !== false ||
            strpos($m, 'slavia') !== false ||
            strpos($m, 'virtus') !== false ||
            strpos($m, 'verna') !== false
        ) {
            return 'city.png';
        }

        // 6. Hatchbacks & Suzuki
        if (
            strpos($c, 'maruti') !== false ||
            strpos($c, 'suzuki') !== false ||
            strpos($m, 'swift') !== false ||
            strpos($m, 'baleno') !== false ||
            strpos($m, 'wagon') !== false ||
            strpos($m, 'brezza') !== false
        ) {
            if (strpos($m, 'alto') !== false || strpos($m, '800') !== false || strpos($m, 'k10') !== false) {
                return 'car2.png';
            }
            return 'swift.png';
        }

        // 7. Hyundai & Creta
        if (
            strpos($c, 'hyundai') !== false ||
            strpos($m, 'creta') !== false ||
            strpos($m, 'venue') !== false ||
            strpos($m, 'alcazar') !== false ||
            strpos($m, 'exter') !== false
        ) {
            return 'creta.png';
        }

        // 8. Luxury Cars (BMW, Mercedes, Audi, Porsche)
        if (
            strpos($c, 'bmw') !== false ||
            strpos($c, 'mercedes') !== false ||
            strpos($c, 'audi') !== false ||
            strpos($c, 'porsche') !== false
        ) {
            return 'car1.png';
        }

        // Default Hero Car
        return 'car-bg.png';
    }
}
