<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
    private string $uploadDir;

    public function __construct(string $projectDir)
    {
        $this->uploadDir = $projectDir . '/public/uploads/threads';
    }

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();

        $file->move($this->uploadDir, $filename);

        return $filename;
    }
}
