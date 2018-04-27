<?php

namespace App\Repositories;

use App\Helpers\Thumbnail;
use App\Models\Company;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CompanyRepositoryEloquent extends BaseRepository implements CompanyRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Company::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams(){
        $this->userRepository = new UserRepositoryEloquent(app());
    }

    public function getAll()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('companies.contactPerson','companies.cities');
        return $org->companies;
    }

    public function uploadAvatar(UploadedFile $file)
    {
        $destinationPath = public_path().'/uploads/company/';
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $fileName = str_random(10).'.'.$extension;

        return $file->move($destinationPath, $fileName);
    }

    public function generateThumbnail($file)
    {
        Thumbnail::generate_image_thumbnail(public_path().'/uploads/company/'.$file->getFileInfo()->getFilename(),
            public_path().'/uploads/company/'.'thumb_'.$file->getFileInfo()->getFilename());
    }

    public function getAllForCustomer($company_id)
    {
        $this->generateParams();
        $companies = $this->userRepository->getOrganization()->companies()->get();
        return $companies;
    }
}
