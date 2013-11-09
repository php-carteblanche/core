<?php

namespace Lib\Manifest\Field;

use \DateTime;
use \Exception;

class Date extends AbstractField
{

    public function validate($entry, $default = null)
    {
        try {
            $dt = new \DateTime( $entry );
            return $entry;
        } catch(Exception $e) {
            return $default;
        }
    }

    public static function getDateTimeFromTimestamp($timestamp)
    {
        $time = new \DateTime;
        $time->setTimestamp( $timestamp );
        return $time;
    }

}

// Endfile