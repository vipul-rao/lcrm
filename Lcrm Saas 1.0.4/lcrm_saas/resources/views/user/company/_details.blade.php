<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label class="control-label" for="title"><b>{{ $company->name }}</b></label>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>{{trans('company.cash_information')}}</h3>

                <div class="row">
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="txt">
                            <strong>{{trans('company.total_sales')}}</strong>
                        </div>
                        <div class="number c-primary">${{$total_sales}} </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="txt">
                            <strong>{{trans('company.open_invoices')}}</strong>
                        </div>
                        <div class="number c-green">${{ $open_invoices}} </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="txt">
                            <strong>{{trans('company.overdue_invoices')}}</strong>
                        </div>
                        <div class="number c-red">${{ $overdue_invoices}} </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="txt">
                            <strong>{{trans('company.paid_invoices')}}</strong>
                        </div>
                        <div class="number c-blue">${{ $paid_invoices}} </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="txt">
                            <strong>{{trans('company.quotations_total')}}</strong>
                        </div>
                        <div class="number c-blue">${{ $quotations_total}} </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 m-t-20">
                <h3>{{trans('company.customer_activities')}}</h3>

                <div class="widget-infobox row">
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">phone</i> {{trans('company.calls')}}</strong>
                        </div>
                        <div class="right">
                            <div class="clearfix">
                                <div>
                                    <span class="c-red pull-left">{{ $calls}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">radio</i> {{trans('company.meeting')}}</strong>
                        </div>
                        <div class="right">
                            <div class="clearfix">
                                <div>
                                    <span class="c-yellow pull-left">{{$meeting}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">attach_money</i> {{trans('company.salesorder')}}</strong>
                        </div>
                        <div class="right">
                            <div>
                                <span class="c-primary pull-left">{{ $salesorder}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">web</i> {{trans('company.invoices')}}</strong>
                        </div>
                        <div class="right">
                            <div class="clearfix">
                                <div>
                                    <span class="c-purple pull-left">{{ $invoices}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">receipt</i> {{trans('company.quotations')}}</strong>
                        </div>
                        <div class="right">
                            <div class="clearfix">
                                <div>
                                    <span class="c-orange pull-left">{{ $quotations}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 m-t-10">
                        <div class="left">
                            <strong><i class="material-icons">email</i> {{trans('company.emails')}}</strong>
                        </div>
                        <div class="right">
                            <div class="clearfix">
                                <div>
                                    <span class="c-purple pull-left">{{ $emails}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 m-t-20">
                <h3>{{trans('company.details')}}</h3>

                <div class="widget-member2 m-t-10">
                    <div class="row">
                        <div class="col-lg-2 col-sm-4 col-12">
                            @if($company->company_avatar)
                                <img src="{{ url('uploads/company/'.$company->company_avatar)}}" alt="profil 4"
                                     class="pull-left img-responsive thumbnail" style="height: 81px;width:81px;">
                            @else
                                <img src=" {{url('uploads/avatar/user.png')}}" alt="user image"
                                     class="pull-left img-responsive thumbnail" style="height: 81px;width:81px;">
                            @endif
                        </div>
                        <div class="col-lg-10 col-sm-8 col-12">
                            <div class="row">
                                @if($company->address)
                                    <div class="col-sm-12">
                                        <p>
                                            <i class="fa fa-map-marker c-gray-light p-r-10"></i> {{ $company->address}}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if(isset($company->website))
                                    <div class="col-xlg-4 col-lg-6 col-sm-4 word-break">
                                        <p>
                                            <i class="fa fa-globe c-gray-light p-r-10 "></i> {{ $company->website}}
                                        </p>
                                    </div>
                                @endif
                                @if(isset($company->email))
                                    <div class="col-xlg-4 col-lg-6 col-sm-4 align-right">
                                        <p>
                                            <i class="fa fa-envelope  c-gray-light"></i> {{ $company->email}}
                                        </p>
                                    </div>
                                @endif
                                @if(isset($company->phone))
                                    <div class="col-xlg-4 col-lg-6 col-sm-4">
                                        <p>
                                            <i class="fa fa-phone c-gray-light"></i> {{  $company->phone}}
                                        </p>
                                    </div>
                                @endif
                                @if(isset($company->fax))
                                    <div class="col-xlg-4 col-lg-6 col-sm-4 align-right">
                                        <p><i class="fa fa-fax c-gray-light"></i> {{ $company->fax}}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(isset($company->contactPerson))
                            <div class="col-xlg-4 col-lg-6 col-sm-4 align-right">
                                <label class="control-label">{{ trans('company.main_contact_person') }}</label>
                                <p>
                                    <i class="icon-user c-gray-light p-r-10"></i> {{ $company->contactPerson->full_name}}
                                </p>
                            </div>
                        @endif
                        @if(isset($company->salesTeam))
                            <div class="col-xlg-4 col-lg-6 col-sm-4">
                                <label class="control-label">{{ trans('company.sales_team_id') }}</label>
                                <p><i class="fa fa-check c-gray-light"></i> {{ $company->salesTeam->salesteam}}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-group">
                <div class="controls">
                    @if (@$action == trans('action.show'))
                        <a href="{{ url($type) }}" class="btn btn-warning"><i
                                    class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    @else
                        <button type="submit" class="btn btn-danger"><i
                                    class="fa fa-trash"></i> {{trans('table.delete')}}
                        </button>
                        <a href="{{ url($type) }}" class="btn btn-warning"><i
                                    class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>