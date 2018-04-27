@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
            <a href="{{url($type.'/'.$subscription->id.'/active')}}" class="btn btn-warning m-b-10"><i
                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">flag</i>
                {{ $title }}
            </h4>
            <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <div class=" table-responsive">
                <table id="data" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('payplan.name') }}</th>
                        <th>{{ trans('payplan.amount') }}</th>
                        <th>{{ trans('payplan.interval') }}</th>
                        <th>{{ trans('payplan.no_people') }}</th>
                        <th>{{ trans('payplan.description') }}</th>
                        <th>{{ trans('table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($settings['stripe_secret']!="" && $settings['stripe_publishable']!="")
                        @foreach($payment_plans_list as $key=>$item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>{{$item->amount/100}} {!! $item['currency'] !!}</td>
                                <td>{{$item->interval}}</td>
                                <td>{{$item->no_people}}</td>
                                <td>{{$item->statement_descriptor}}</td>
                                <td>
                                    @if($item->plan_id != $active_plan->plan_id)
                                        @if(isset($organization->staffWithUser) && (($organization->staffWithUser->count() + $unanswered_invites) > $item->no_people)
                                        && $item->no_people)
                                            <button class="btn btn-warning disabled" data-toggle="tooltip"
                                                    title="{{ trans('subscription.current_number_of_users').' '.($organization->staffWithUser->count() + $unanswered_invites).'. '.trans('subscription.exceeds_people_in_plan')}} .">
                                                <i class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}
                                            </button>
                                        @else
                                            {!! Form::open(['url' => $type.'/'.$subscription->id.'/change_plan/'.$item->id, 'method' => 'post']) !!}
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}
                                            </button>
                                            {!! Form::close() !!}
                                        @endif
                                    @else
                                        @if(isset($organization)&&count($organization->subscriptions)&&$organization->subscriptions->first()->ends_at)
                                            {!! Form::open(['url' => $type.'/'.$subscription->id.'/change_plan/'.$item->id, 'method' => 'post']) !!}
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}
                                            </button>
                                            {!! Form::close() !!}
                                            <div class="badge badge-primary pull-left m-t-10">
                                                {{ trans('subscription.previous_plan') }}
                                            </div>
                                        @else
                                            <button class="btn btn-success">
                                                {{ trans('subscription.current_plan') }}
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop
@section('scripts')
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
