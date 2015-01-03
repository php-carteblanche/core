<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Model;

/**
 * Helper functions for the IMAGE MODEL
 */
class ImageModelHelper
{

    /**
     */
    public static function getGps( $exifCoord, $hemi )
    {
        $degrees = count($exifCoord) > 0 ? self::gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? self::gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? self::gps2Num($exifCoord[2]) : 0;
        $flip = ($hemi == 'W' || $hemi == 'S') ? -1 : 1;
        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    /**
     */
    public static function gps2Num( $coordPart )
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) return 0;
        if (count($parts) == 1) return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }

    /**
     * La date est d'un format spécial, on va donc la rendre lisible
     */
    public static function getDateFromExif( $date )
    {
        $date_full = explode(":", current(explode(" ", $date)));
        $hour_full = explode(":", next(explode(" ", $date)));
        $year = current($date_full);
        $month = next($date_full);
        $day = next($date_full);
        $hour = current($hour_full);
        $minutes = next($hour_full);
        $seconds = next($hour_full);
        //	  return "$year-$month-$day $hour:$minutes:$seconds";
        return "$day-$month-$year ${hour}h$minutes:$seconds";
    }

}

// Endfile