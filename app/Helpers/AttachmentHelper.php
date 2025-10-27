<?php 
namespace App\Helpers;
use Carbon\Carbon;
 
class AttachmentHelper
{

   /**
     * Convert bytes into KB, MB, or GB.
     *
     * @param int|float $bytes      The size in bytes.
     * @param string    $unit       The desired unit: 'KB', 'MB', or 'GB'.
     * @param int       $precision  Number of decimal places (default 2).
     *
     * @return string
     */
    static function convertBytes($bytes, $unit = 'MB', $precision = 2)
    {
        if ($bytes < 0) {
            return 'Invalid value';
        }

        switch (strtoupper($unit)) {
            case 'KB':
                $result = $bytes / 1024;
                break;
            case 'MB':
                $result = $bytes / (1024 ** 2);
                break;
            case 'GB':
                $result = $bytes / (1024 ** 3);
                break;
            default:
                return 'Invalid unit (use KB, MB, or GB)';
        }

        return round($result, $precision) . ' ' . strtoupper($unit);
    }
}
