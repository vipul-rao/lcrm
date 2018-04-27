<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.name')}}</label>
                    <div class="controls">
                        {{ $payplan->name }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.amount')}}</label>
                    <div class="controls">
                        {{ $payplan->amount }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.currency')}}</label>
                    <div class="controls">
                        {{ $payplan->currency }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.interval')}}</label>
                    <div class="controls">
                        {{ $payplan->interval_count.' '.$payplan->interval.'s' }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.no_people')}}</label>
                    <div class="controls">
                        {{ 0 !== $payplan->no_people ? $payplan->no_people : trans('payplan.no_people_info') }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.trial_period_days')}}</label>
                    <div class="controls">
                        {{ $payplan->trial_period_days ? $payplan->trial_period_days.' '.trans('payplan.days') : trans('payplan.no_trial') }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.visibility')}}</label>
                    <div class="controls">
                        {{ isset($payplan)&&$payplan->is_visible==1? trans('payplan.visible'): trans('payplan.not_visible') }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.organizations')}}</label>
                    <div class="controls">
                        {{ $payplan->organizations }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('payplan.description')}}</label>
                    <div class="controls">
                        {{ $payplan->statement_descriptor }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == trans('action.show'))
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @else
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}</button>
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="float-left">
            {{ trans('organizations.organizations') }}
        </h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('subscription.org_name') }}</th>
                    <th>{{ trans('subscription.org_email') }}</th>
                    <th>{{ trans('subscription.plan') }}</th>
                    <th>{{ trans('subscription.subscription_type') }}</th>
                    <th>{{ trans('subscription.end_subscription') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subscriptions_data as $data)
                    <tr>
                        <td>
                            {{ isset($data->organization->name)?$data->organization->name:null }}
                        </td>
                        <td>
                            {{ isset($data->organization->email)?$data->organization->email:null }}
                        </td>
                        <td>
                            {{ $data->name }}
                        </td>
                        <td>
                            {{ $data->subscription_type??'--' }}
                        </td>
                        <td>
                            {{ $data->ends_at ? date(config('settings.date_time_format'),strtotime($data->ends_at)) : trans('subscription.subscription_active') }}
                        </td>
                    </tr>
                    @endforeach
                @foreach($subscriptions_data_paypal as $data)
                    <tr>
                        <td>
                            {{ isset($data->organization->name)?$data->organization->name:null }}
                        </td>
                        <td>
                            {{ isset($data->organization->email)?$data->organization->email:null }}
                        </td>
                        <td>
                            {{ $data->name }}
                        </td>
                        <td>
                            {{ $data->subscription_type??'--' }}
                        </td>
                        <td>
                            {{ $data->ends_at ? date(config('settings.date_time_format'),strtotime($data->ends_at)) : trans('subscription.subscription_active') }}
                        </td>
                    </tr>
                @endforeach
                @foreach($organizations_data as $data)
                    <tr>
                        <td>
                            {{ $data->name }}
                        </td>
                        <td>
                            {{ $data->email }}
                        </td>
                        <td>
                            {{ isset($data->genericPlan) ? $data->genericPlan->name : null }}
                        </td>
                        <td>
                            {{ $data->subscription_type??'--' }}
                        </td>
                        <td>
                            {{ $data->trial_ends ? $data->trial_ends : trans('subscription.subscription_active') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#data').DataTable();
        });
    </script>
@stop
