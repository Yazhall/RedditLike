<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Thread;
use App\Entity\ThreadFile;
use Doctrine\ORM\EntityManagerInterface;

class ThreadFileService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploaderService $uploader,
    ) {}

    public function attachUploadsToThread(Thread $thread, array $uploads): void
    {
        foreach ($uploads as $fileUpload) {

            $filename = $this->uploader->upload($fileUpload);


            $file = new File();
            $file->setOriginalName($fileUpload->getClientOriginalName());
            $file->setPath('/uploads/threads/' . $filename);
            $this->entityManager->persist($file);


            $threadFile = new ThreadFile();
            $threadFile->setThread($thread);
            $threadFile->setFile($file);
            $this->entityManager->persist($threadFile);
        }
    }
}
