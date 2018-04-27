<?php

namespace App\Repositories;

use App\Helpers\Thumbnail;
use App\Models\Organization;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class OrganizationRepositoryEloquent.
 */
class OrganizationRepositoryEloquent extends BaseRepository implements OrganizationRepository
{
    private $userRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Organization::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams()
    {
        $this->userRepository = new UserRepositoryEloquent(app());
    }

    public function uploadLogo(UploadedFile $file)
    {
        $destinationPath = public_path().'/uploads/organizations/';
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $fileName = str_random(10).'.'.$extension;

        return $file->move($destinationPath, $fileName);
    }

    public function generateThumbnail($file)
    {
        Thumbnail::generate_image_thumbnail(public_path().'/uploads/organizations/'.$file->getFileInfo()->getFilename(),
            public_path().'/uploads/organizations/'.'thumb_'.$file->getFileInfo()->getFilename());
    }

    public function getStaff()
    {
        $this->generateParams();
        $organization = $this->userRepository->getOrganization();

        return $organization->staff();
    }

    public function getStaffWithUser()
    {
        $this->generateParams();
        $organization = $this->userRepository->getOrganization();

        return $organization->staffWithUser();
    }

    public function getCustomers()
    {
        $this->generateParams();
        $organization = $this->userRepository->getOrganization();

        return $organization->roleCustomers()->with('customer');
    }

    public function getUserStaffCustomers()
    {
        $this->generateParams();
        $organization = $this->userRepository->getOrganization();

        return $organization->UserStaffCustomers();
    }

    public function getMonth($created_at)
    {
        $organization = $this->model->whereMonth('created_at', $created_at)->get();

        return $organization;
    }

    public function getMonthYear($monthno, $year)
    {
        $organization = $this->model->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();

        return $organization;
    }

    public function organizationPayments()
    {
        $payments = $this->model->where('created_by_admin', 1)->get();

        return $payments;
    }

    public function onGenericTrial()
    {
        $genericTrial = $this->model->where([
            ['created_by_admin', 0],
            ['card_last_four', null],
        ])->whereDate('trial_ends_at', '>', now())->get();

        return $genericTrial;
    }

    public function ExpiredGenericTrial()
    {
        $genericTrialExpired = $this->model->where([
            ['created_by_admin', 0],
            ['card_last_four', null],
        ])->whereDate('trial_ends_at', '<', now())->get();

        return $genericTrialExpired;
    }
}
