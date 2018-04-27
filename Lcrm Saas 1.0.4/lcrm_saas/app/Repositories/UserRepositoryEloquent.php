<?php

namespace App\Repositories;

use App\Events\User\UserCreated;
use App\Helpers\Thumbnail;
use App\Models\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sentinel;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    public function model()
    {
        return User::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function check()
    {
        return Sentinel::check();
    }

    public function getUser()
    {
        return Sentinel::getUser();
    }

    public function logout($user = null, $everywhere = false)
    {
        Sentinel::logout($user, $everywhere);

        return;
    }

    public function authenticate($request, $remember = null)
    {
        $user = Sentinel::authenticate($request, $remember);

        return $user;
    }

    public function login(User $user)
    {
        $user = Sentinel::login($user);

        return $user;
    }

    public function inRole($role, User $user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }

        return $user ? $user->inRole($role) : false;
    }

    public function getOrganization(User $user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }

        $isAdmin = $this->inRole('admin', $user);

        return $isAdmin ? null : ($user ? $user->organizations()->first() : null);
    }

    public function getWithCustomer(User $user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }

        return $user->with(['organizations.customers.company'])->find($user->id);
    }

    public function uploadAvatar(UploadedFile $file)
    {
        $destinationPath = public_path().'/uploads/avatar/';
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $fileName = str_random(10).'.'.$extension;

        return $file->move($destinationPath, $fileName);
    }

    public function generateThumbnail($file)
    {
        Thumbnail::generate_image_thumbnail(public_path().'/uploads/avatar/'.$file->getFileInfo()->getFilename(),
            public_path().'/uploads/avatar/'.'thumb_'.$file->getFileInfo()->getFilename());
    }

    public function create(array $data, $activate = false)
    {
        $user = Sentinel::register($data, $activate);
        event(new UserCreated($user));

        return $user;
    }

    public function assignRole(User $user, $roleName)
    {
        $role = Sentinel::getRoleRepository()->findByName($roleName);
        $role->users()->attach($user);
    }

    public function getRoles()
    {
        return Sentinel::getRoleRepository()->get();
    }

    public function getUsers()
    {
        $role = Sentinel::findRoleBySlug('user');

        $admin = $role->users()->with('roles')->get();

        return $admin;
    }

    public function getAdmins()
    {
        $role = Sentinel::findRoleBySlug('admin');

        $admin = $role->users()->with('roles')->get();

        return $admin;
    }

    public function getMonth($created_at)
    {
        $users = $this->model->whereMonth('created_at', $created_at)->get();

        return $users;
    }

    public function usersWithTrashed($email)
    {
        $users = $this->model->withTrashed()->where('email', $email)->get();

        return $users;
    }

    public function findRoleBySlug($slug)
    {
        $role = Sentinel::findRoleBySlug($slug);
        return $role;
    }
}
