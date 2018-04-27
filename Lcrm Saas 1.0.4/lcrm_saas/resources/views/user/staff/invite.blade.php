@extends('layouts.user')
@section('title')
    {{ $title }}
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            @include('flash::message')
            {!! Form::open(['url' => $type.'/invite', 'method' => 'post', 'files'=> true]) !!}
            <div class="form-group required {{ $errors->has('emails') ? 'has-error' : '' }}">
                {!! Form::label('email', trans('staff.emails'), ['class' => 'control-label required']) !!}
                <div class="controls">
                    {!! Form::text('emails', null, ['class' => 'form-control']) !!}
                    <span class="help-block">{{ $errors->first('emails', ':message') }}</span>
                </div>
            </div>
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                    <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable no-footer">
                            <thead>
                            <tr>
                                <th>{{trans('staff.email')}}</th>
                                <th>{{trans('staff.send_invitation')}}</th>
                                <th>{{trans('staff.accept_invitation')}}</th>
                                <th>{{trans('staff.cancel_invitation')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($user->invite as $item)
                                <tr>
                                    <td>{{$item->email}}</td>
                                    <td>{{$item->created_at->format(config('settings.date_format'))}}</td>
                                    <td>{{isset($item->claimed_at)?Carbon\Carbon::parse($item->claimed_at)->format(config('settings.date_format')):""}}</td>
                                    <td>
                                        @if(!isset($item->claimed_at))
                                            <a href="{{url('/staff/invite/'.$item->id.'/cancel')}}" class="btn-link"><i class="fa fa-times text-danger"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
