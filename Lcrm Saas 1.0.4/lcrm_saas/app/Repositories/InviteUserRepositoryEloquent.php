<?php

namespace App\Repositories;

use App\Models\InviteUser;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class InviteUserRepositoryEloquent extends BaseRepository implements InviteUserRepository
{
    private $userRepository;

    public function model()
    {
        return InviteUser::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams()
    {
        $this->userRepository = new UserRepositoryEloquent(app());
    }

    public function createInvite(array $data)
    {
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['code'] = bin2hex(openssl_random_pseudo_bytes(16));
        $data['organization_id'] = $organization->id;

        return $user->invite()->create($data);
    }
}
