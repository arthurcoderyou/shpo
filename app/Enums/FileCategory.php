<?php 

// app/Enums/FileCategory.php
namespace App\Enums;

enum FileCategory: string
{
    case image = 'image';
    case video = 'video';
    case audio = 'audio';
    case pdf   = 'pdf';
    case word  = 'word';
    case excel = 'excel';
    case ppt   = 'ppt';
    case text  = 'text';
    case archive = 'archive';
    case other = 'other';
}