<?php

namespace App\Repositories;

use App\Models\Qtemplate;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class QuotationTemplateRepositoryEloquent extends BaseRepository implements QuotationTemplateRepository
{
    private $userRepository;

    public function model()
    {
        return Qtemplate::class;
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
        $qtemplates = $this->userRepository->getOrganization()->qtemplates()->get();
        return $qtemplates;
    }

    public function createQtemplate(array $data)
    {
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id']= $user->id;
        $data['organization_id']= $organization->id;

        $team = collect($data)->except('product_list','taxes','product_id','product_name','description','quantity','price','sub_total')->toArray();
        $qtemplate = $this->create($team);
        $list =[];

        foreach ($data['product_id']as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $qtemplate->qTemplateProducts()->attach($list);
    }

    public function updateQtemplate(array $data,$qtemplate_id)
    {
        $this->generateParams();
        $team = collect($data)->except('product_list','taxes','product_id','product_name','description','quantity','price','sub_total')->toArray();
        $qtemplate = $this->update($team,$qtemplate_id);
        $list =[];

        foreach ($data['product_id']as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $qtemplate->qTemplateProducts()->sync($list);
    }

    public function deleteQtemplate($deleteQtemplate)
    {
        $this->generateParams();
//        Remove qtemplate products
        $qtemplateProduct = $this->find($deleteQtemplate);
        $qtemplateProduct->qTemplateProducts()->detach();
        $this->delete($deleteQtemplate);
    }
}
