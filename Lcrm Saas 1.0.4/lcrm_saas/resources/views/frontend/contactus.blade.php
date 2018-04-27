@extends('layouts.frontend.user')
@section('styles')
@stop
@section('content')
    <div class="container">
        <div class="row bloglist_margin_block bloglist_margintop">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 col-12">
                <div class="map"></div>
            </div>
            <div class="col-sm-5 col-md-5 col-xl-5 col-lg-5 ml-auto col-12">
                <h3 class="mb-4 mt-5">{{ trans('frontend.get_in_touch') }}</h3>
                <p class="contact_get"><span class="blockquote_color">{{ trans('frontend.get_in_touch_description') }}</span></p>
                <p class="contact_get">{{ trans('frontend.address') }}: <span class="contact1_span"><b>{{$settings['address']??''}}</b></span><br />
                    {{ trans('frontend.phone') }}: <span class="contact1_span"><b>{{$settings['phone_number']??''}}</b></span><br />
                    {{ trans('frontend.email') }}: <span class="text_primary">{{$settings['site_email']??''}}</span><br /></p>
                <p> {{ trans('frontend.monday_to_friday_from') }} <span class="contact1_span"><b>9.00 am to 8.00 pm EST</b></span><br />
                    {{ trans('frontend.saturday_from') }} <span class="contact1_span"><b>10.00 am to 6.00 pm EST</b></span></p>
            </div>
        </div>
    </div>
    <div class="container contact_form contact_margin">
        <h3 class="mb-5">FeedBack</h3>
        {!! Form::open(['url' => url('contactus'), 'method' => 'post']) !!}
            <div class="row ">
                <div class="col-12 col-sm-4 form-group stdpost_form-group">
                    <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                        {!! Form::label('name', trans('contactus.name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('name', isset($user)?$user->first_name.' '.$user->last_name:null, ['class' => 'form-control','placeholder'=>trans('contactus.name')]) !!}
                            <span class="help-block">{{ $errors->first('name', ':message') }}</span>
                        </div>
                    </div>
                    <i class="fa fa-check check"></i>
                </div>
                <div class="col-12 col-sm-4 form-group stdpost_form-group">
                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::label('email', trans('contactus.email'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('email', isset($user)?$user->email:null, ['class' => 'form-control','placeholder'=>trans('contactus.email')]) !!}
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                        </div>
                    </div>
                    <i class="fa fa-check check"></i>
                </div>

                <div class="col-12 col-sm-4 form-group stdpost_form-group">
                    <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                        {!! Form::label('phone_number', trans('contactus.phone_number'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('phone_number', isset($user)?$user->phone_number:null, ['class' => 'form-control','placeholder'=>trans('contactus.phone_number')]) !!}
                            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-12 form-group stdpost_form-group">
                    <div class="form-group required {{ $errors->has('message') ? 'has-error' : '' }}">
                        {!! Form::label('message', trans('contactus.message'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::textarea('message', null, ['class' => 'form-control','placeholder'=>trans('contactus.message')]) !!}
                            <span class="help-block">{{ $errors->first('message', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-12">
                <button  class="btn btn-primary button_postcomment" type="submit">SEND MESSAGE</button>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
@stop
@section('scripts')
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key={{config('services.gmaps_key')}}&libraries=places"></script>
    <script src="{{ asset('front/vendors/gmap3/js/gmap3.min.js') }}"></script>
    <script>
            var latitude = '{{ $settings['latitude'] ?? '-37.7681102' }}';
            var longitude = '{{ $settings['longitude'] ?? '144.8378658' }}';
    </script>
    <script src="{{ asset('front/js/contact_us.js') }}"></script>
@stop
