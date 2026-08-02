<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

/**
 * The saved filename's extension must always come from the file's real,
 * content-detected MIME type — never from the client-supplied filename
 * (UploadedFile::getClientOriginalExtension()) — or a polyglot file (valid
 * image/document bytes with an appended PHP payload) can be saved as
 * executable code inside the public web root. This is the fix for a past
 * Critical RCE finding where that correct pattern existed in one controller
 * but wasn't applied to three sibling upload endpoints; every file upload
 * in the app should resolve its extension through this class instead of
 * keeping its own copy of the MIME map.
 */
class SafeUpload
{
    private const IMAGE_MIME_EXTENSIONS = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    private const DOCUMENT_MIME_EXTENSIONS = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public static function imageExtension(UploadedFile $file): ?string
    {
        return self::IMAGE_MIME_EXTENSIONS[$file->getMimeType()] ?? null;
    }

    public static function documentExtension(UploadedFile $file): ?string
    {
        return self::DOCUMENT_MIME_EXTENSIONS[$file->getMimeType()] ?? null;
    }
}
