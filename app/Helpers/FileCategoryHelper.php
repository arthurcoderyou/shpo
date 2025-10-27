<?php 
namespace App\Helpers;
use Carbon\Carbon; 
use App\Models\Project; 



class FileCategoryHelper
{
    // public static function submit()
    // {
    //     // your helper logic
    // }

    public static function categorize(?string $mime, string $ext): string
    {
        $ext = strtolower($ext);
        $mime = strtolower($mime ?? '');

        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'video/')) return 'video';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        if ($mime === 'application/pdf' || $ext === 'pdf') return 'pdf';
        if (in_array($ext, ['doc','docx'])) return 'word';
        if (in_array($ext, ['xls','xlsx','csv'])) return 'excel';
        if (in_array($ext, ['ppt','pptx'])) return 'ppt';
        if (in_array($ext, ['txt','md','rtf'])) return 'text';
        if (in_array($ext, ['zip','rar','7z','tar','gz'])) return 'archive';
        return 'other';
    }
 
 
 
    public static function checkNavigationIfActive(){

        

    }

}   