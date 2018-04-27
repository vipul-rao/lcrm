<?php

namespace App\Repositories;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Prettus\Repository\Contracts\RepositoryInterface;


interface CompanyRepository extends RepositoryInterface
{
    public function getAll();

    public function uploadAvatar(UploadedFile $file);

    public function generateThumbnail($file);

    public function getAllForCustomer($company_id);
}