<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\OpportunityRepository;
use Yajra\Datatables\Datatables;

class OpportunityDeleteListController extends Controller
{
    private $opportunityRepository;
    protected $user;

    public function __construct(OpportunityRepository $opportunityRepository)
    {
        parent::__construct();
        $this->opportunityRepository = $opportunityRepository;

        view()->share('type', 'opportunity_delete_list');
    }
    public function index()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('opportunity.delete_list');
        return view('user.opportunity_delete_list.index',compact('title'));
    }

    public function show($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getDeleted()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.show_delete_list');
        $action = trans('action.show');
        return view('user.opportunity_delete_list.show', compact('title', 'opportunity','action'));
    }

    public function delete($opportunity){
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getDeleted()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.restore_delete_list');
        $action = 'delete';
        return view('user.opportunity_delete_list.restore', compact('title', 'opportunity','action'));
    }

    public function restoreOpportunity($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getDeleted()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $opportunity->update(['is_delete_list'=>0]);
        return redirect('opportunity');
    }

    public function data(Datatables $datatables)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $opportunityDeleteList = $this->opportunityRepository->getDeleted()
            ->map(function ($opportunityDeleteList) use ($orgRole,$dateFormat) {
                return [
                    'id' => $opportunityDeleteList->id,
                    'opportunity' => $opportunityDeleteList->opportunity,
                    'company' => $opportunityDeleteList->companies->name ?? null,
                    'customer' => $opportunityDeleteList->customer->full_name ?? null,
                    'next_action' => date($dateFormat,strtotime($opportunityDeleteList->next_action)),
                    'stages' => $opportunityDeleteList->stages,
                    'expected_revenue' => $opportunityDeleteList->expected_revenue,
                    'probability' => $opportunityDeleteList->probability,
                    'salesteam' => $opportunityDeleteList->salesTeam->salesteam ?? null,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($opportunityDeleteList)

            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'opportunities.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'opportunity_delete_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @endif
                                            @if(Sentinel::getUser()->hasAccess([\'opportunities.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'opportunity_delete_list/\' . $id . \'/restore\' ) }}"  title="{{ trans(\'table.restore\') }}">
                                            <i class="fa fa-fw fa-undo text-success"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    private function generateParams(){
        $this->user = $this->getUser();
    }
}
