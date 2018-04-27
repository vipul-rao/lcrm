<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface OrganizationRepository.
 */
interface OrganizationRepository extends RepositoryInterface
{
    public function uploadLogo(UploadedFile $file);

    public function generateThumbnail($file);

    public function getStaff();

    public function getCustomers();

    public function getStaffWithUser();

    public function getUserStaffCustomers();

    public function getMonth($created_at);

    public function getMonthYear($monthno, $year);

    public function organizationPayments();

    public function onGenericTrial();

    public function ExpiredGenericTrial();
}
