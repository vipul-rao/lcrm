<?php

namespace App\Repositories;

use App\Models\InvoiceReceivePayment;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class InvoicePaymentRepositoryEloquent extends BaseRepository implements InvoicePaymentRepository
{
    private $userRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return InvoiceReceivePayment::class;
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

    public function getAll()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('invoiceReceivePayments.invoice.companies');
        $invoicesPayment = $org->invoiceReceivePayments;

        return $invoicesPayment;
    }

    public function createPayment(array $data)
    {
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id'] = $user->id;
        $data['organization_id'] = $organization->id;

        $team = collect($data)->toArray();
        $invoice_payment = $this->create($team);

        return $invoice_payment;
    }

    public function getAllPaidForCustomer($company_id)
    {
        $this->generateParams();
        $invoice_payment = $this->userRepository->getOrganization()->invoiceReceivePayments()->where([
            ['company_id','=', $company_id],
        ])->get();
        return $invoice_payment;
    }
}
