@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <a class="btn btn-sm btn-primary"
           href="{!! url('first_pay') !!}">{{trans('userpayment.subscription_new')}}</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped b-t">
                <thead>
                <tr>
                    <th>{{trans('payment.payplan')}}</th>
                    <th>{{trans('payment.status')}}</th>
                    <th>{{trans('payment.subscription_ends')}}</th>
                    <th>{{trans('table.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @if($user->subscriptions->count()>0)
                    @foreach($user->subscriptions as $subscription)
                        <tr>
                            <td>{{ isset($subscription->stripe_plan)?$subscription->payplan->name:"-" }}</td>
                            <td>{{ $subscription->status }}</td>
                            <td>{{ $subscription->ended_at }}</td>
                            <td>
                                @if($subscription->status!='Cancel')
                                    <a class="btn btn-sm btn-warning"
                                       href="{!! url('payment/'.$subscription->id.'/cancel') !!}">{{trans('userpayment.subscription_cancel')}}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>


        </div>
    </div>
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function($) {
            $('.btn-warning').on('click', function () {
                return confirm("Are you sure you want to cancel this subscription?");
            });
        });
    </script>
@stop
