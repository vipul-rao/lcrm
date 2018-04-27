@extends('layouts.user')
{{-- Web site Title --}}
@section('title')
{{ $title }}
@stop
{{-- Content --}}
@section('content')
<div class="page-header clearfix">
    <div class="row">
        <div class="col-md-12">
            @include('flash::message')
        </div>
    </div>
    <div class="pull-right">
        <a href="{{url($type)}}" class="btn btn-warning m-b-10">
            <i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
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
                        @foreach($payment_plans_list as $key=>$item)
                            @if($item->plan_id == $active_subscription->stripe_plan || $active_subscription->payplan_id==$item->id)
                                <tr>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->amount/100}} {!! $item['currency'] !!}</td>
                                    <td>
                                        {{ ($item->interval_count==1?$item->interval_count.' '.$item->interval:$item->interval_count.' '.$item->interval.'s') }}
                                    </td>
                                    <td>{{$item->no_people}}</td>
                                    <td>{{$item->statement_descriptor}}</td>
                                    <td>
                                        @if(isset($active_subscription))
                                            @if($active_subscription->subscription_type=='stripe')
                                                @if(isset($active_subscription->ends_at))
                                                    @if($active_subscription->ends_at > now())
                                                        {!! Form::open(['url' => $type.'/'.$subscription->id.'/resume', 'method' => 'post']) !!}
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fa fa-undo"></i> {{trans('subscription.resume')}}</button>
                                                        {!! Form::close() !!}
                                                    @endif
                                                @else
                                                    {!! Form::open(['url' => $type.'/'.$subscription->id.'/cancel', 'method' => 'post']) !!}
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa fa-trash"></i> {{trans('subscription.cancel')}}</button>
                                                    {!! Form::close() !!}
                                                @endif
                                                <a href="{{url($type.'/'.$subscription->id.'/change')}}" class="btn btn-warning m-t-10">
                                                    <i class="fa fa-edit"></i> {{trans('subscription.change')}}
                                                </a>
                                                <a href="{{url($type.'/'.$subscription->id.'/extend')}}" class="btn btn-info m-t-10" title="Add days to the Organizations without billing">
                                                    <i class="fa fa-plus-square"></i> {{trans('subscription.extend')}}
                                                </a>
                                            @else
                                                @if($active_subscription->status=='Suspended')
                                                    {!! Form::open(['url' => $type.'/'.$subscription->id.'/resume', 'method' => 'post']) !!}
                                                    <button type="submit" class="btn btn-success m-b-10"><i
                                                                class="fa fa-undo"></i> {{trans('subscription.reactivate')}}</button>
                                                    {!! Form::close() !!}
                                                @else
                                                    @if($active_subscription->status!=='Canceled')
                                                        {!! Form::open(['url' => $type.'/'.$subscription->id.'/cancel', 'method' => 'post']) !!}
                                                        <button type="submit" class="btn btn-danger m-b-10">
                                                            <i class="fa fa-trash"></i> Cancel
                                                        </button>
                                                        {!! Form::close() !!}
                                                        {!! Form::open(['url' => $type.'/'.$subscription->id.'/suspend', 'method' => 'post']) !!}
                                                        <button type="submit" class="btn btn-primary m-b-10">
                                                            <i class="fa fa-ban"></i> Suspend
                                                        </button>
                                                        {!! Form::close() !!}
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    {{--@endif--}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
