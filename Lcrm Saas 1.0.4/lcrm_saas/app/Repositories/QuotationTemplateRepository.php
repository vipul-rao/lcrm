<?php namespace
App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface QuotationTemplateRepository extends RepositoryInterface
{
    public function getAll();

    public function createQtemplate(array $data);

    public function deleteQtemplate($deleteQtemplate);

    public function updateQtemplate(array $data,$qtemplate_id);


}