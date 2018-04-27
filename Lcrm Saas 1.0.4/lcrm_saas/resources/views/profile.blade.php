@extends('layouts.user')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-4 col-md-3 m-b-10">
                    <a class="thumbnail">
                        @if(isset($user->user_avatar))
                            <img src="{{ url('uploads/avatar/thumb_'.$user->user_avatar) }}" alt="User Image" class="img-fluid" >
                        @else
                            <img src="{{ url('uploads/avatar/user.png') }}" alt="User Image" class="img-fluid">
                        @endif
                    </a>
                </div>
                <div class="col-12 col-sm-8 col-md-9 m-auto">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td><b>{{trans('profile.first_name')}}</b></td>
                                <td><a href="#"> {{$user->first_name}}</a></td>
                            </tr>
                            <tr>
                                <td><b>{{trans('profile.last_name')}}</b></td>
                                <td><a href="#"> {{$user->last_name}}</a></td>
                            </tr>
                            <tr>
                                <td><b>{{trans('profile.email')}}</b></td>
                                <td><a href="#">{{$user->email}}</a></td>
                            </tr>
                            <tr>
                                <td><b>{{trans('profile.phone_number')}}</b></td>
                                <td><a href="#"> {{$user->phone_number}}</a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{url('account')}}" class="btn btn-success prof-btn">
                        <i class="fa fa-pencil-square-o"></i> {{trans('profile.change_profile')}}</a>
                </div>
            </div>
        </div>
    </div>
@stop
