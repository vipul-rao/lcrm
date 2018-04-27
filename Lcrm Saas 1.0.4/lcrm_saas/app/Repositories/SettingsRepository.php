<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface SettingsRepository.
 */
interface SettingsRepository extends RepositoryInterface
{
    public function getAll();

    public function getKey($key, $default = null);

    public function setKey($key, $value);

    public function forgetKey($key);

    public function uploadLogo(UploadedFile $file);

    public function generateThumbnail($file);
}
