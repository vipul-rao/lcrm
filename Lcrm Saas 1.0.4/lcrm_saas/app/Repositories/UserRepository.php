<?php

namespace App\Repositories;

use App\Models\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository.
 */
interface UserRepository extends RepositoryInterface
{
    public function check();

    public function authenticate($request, $remember=null);

    public function login(User $user);

    public function getUser();

    public function logout($user = null, $everywhere = false);

    public function getWithCustomer(User $user = null);

    public function uploadAvatar(UploadedFile $file);

    public function generateThumbnail($file);

    public function create(array $data, $activate = false);

    public function assignRole(User $user, $roleName);

    public function inRole($role, User $user = null);

    public function getOrganization(User $user);

    public function getUsers();

    public function getAdmins();

    public function getMonth($created_at);

    public function usersWithTrashed($email);

    public function findRoleBySlug($slug);
}
