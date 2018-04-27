<div class="card">
    <div class="card-body">
        @if (isset($customer))
            {!! Form::model($customer, ['url' => $type . '/' . $customer->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('company_avatar_file') ? 'has-error' : '' }}">
                    {!! Form::label('company_avatar_file', trans('customer.profile_picture'), ['class' => 'control-label']) !!}
                    <div class="controls row">
                        <div class="col-sm-6 col-lg-4">
                            <div class="row">
                                @if(isset($customer->user->user_avatar))
                                    <image-upload name="user_avatar_file" old-image="{{ url('uploads/avatar/thumb_'.$customer->user->user_avatar) }}"></image-upload>
                                @else
                                    <image-upload name="user_avatar_file"></image-upload>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <span class="help-block">{{ $errors->first('company_avatar_file', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
                        {!! Form::label('title', trans('customer.title'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('title', $titles, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group required {{ $errors->has('first_name') ? 'has-error' : '' }}">
                        {!! Form::label('first_name', trans('customer.first_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('first_name',(isset($customer->user->first_name)?$customer->user->first_name:null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group required {{ $errors->has('last_name') ? 'has-error' : '' }}">
                        {!! Form::label('last_name', trans('customer.last_name'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('last_name', (isset($customer->user->last_name)?$customer->user->last_name:null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('job_position') ? 'has-error' : '' }}">
                        {!! Form::label('job_position', trans('customer.job_position'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('job_position', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('job_position', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('company_id') ? 'has-error' : '' }}">
                        {!! Form::label('company_id', trans('customer.company'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('company_id', $companies, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('company_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::label('email', trans('customer.email'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::email('email', ($customer->user->email ?? null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                        {!! Form::label('phone_number', trans('customer.phone'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('phone_number', ($customer->user->phone_number ?? null), ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('mobile') ? 'has-error' : '' }}">
                        {!! Form::label('mobile', trans('customer.mobile'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('mobile', ($user->customer->mobile ?? null), ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                            <span class="help-block">{{ $errors->first('mobile', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('password') ? 'has-error' : '' }}">
                        {!! Form::label('password', trans('customer.password'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                        {!! Form::label('password_confirmation', trans('customer.password_confirmation'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('address') ? 'has-error' : '' }}">
                        {!! Form::label('address', trans('customer.address'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('address', ($user->customer->address ?? null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <!-- Form Actions -->
                    <div class="form-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                            <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        </div>
                    </div>
                    <!-- ./ form actions -->
                </div>
            </div>

        {!! Form::close() !!}
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function(){
            $("#title").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('customer.title') }}"
            });
            $("#company_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('customer.company') }}"
            });
            $("#sales_team_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('customer.sales_team_id') }}"
            });
        })
    </script>
    @endsection