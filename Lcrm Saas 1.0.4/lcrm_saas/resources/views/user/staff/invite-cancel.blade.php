@extends('layouts.user')
@section('title')
    {{ $title }}
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            @include('flash::message')
            @if(!isset($invite->claimed_at))
            Email : {{$invite->email}}

            <form action="{{url('/staff/invite/'.$invite->id.'/cancel-invite')}}" method="POST">
                {{csrf_field()}}
                <button class="btn btn-danger">{{trans('staff.invite_cancel')}}</button>
            </form>
            @endif
        </div>
    </div>
@endsection
