<?php namespace
App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface MeetingRepository extends RepositoryInterface
{
    public function getAll();
}