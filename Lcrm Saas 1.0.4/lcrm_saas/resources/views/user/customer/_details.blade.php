<div class="card">
    <div class="card-body">

        <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-3">
                <div class="image_upload thumbnail" >
                    @if(isset($customer->user->user_avatar) && $customer->user->user_avatar!="")
                        <img src="{{ url('uploads/avatar/thumb_'.$customer->user->user_avatar) }}"
                             alt="Image" class="ima-responsive" width="300">
                    @endif
                </div>
            </div>
            <div class="col-sm-7 col-md-8 col-lg-9">
                <div class="form-group">
                    {!! Form::label('last_name', trans('customer.full_name'), ['class' => 'control-label']) !!}
                    : {{isset($customer->user->full_name)?$customer->title.' '.$customer->user->full_name:null }}
                </div>
                <div class="form-group">
                    {!! Form::label('email', trans('customer.email'), ['class' => 'control-label']) !!}
                    : {{ $customer->user->email ?? null }}
                </div>
                <div class="form-group">
                    {!! Form::label('phone', trans('customer.phone'), ['class' => 'control-label']) !!}
                    : {{ $customer->user->phone_number ?? null }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-lg-3">
                <div class="form-group">
                    {!! Form::label('job_position', trans('customer.job_position'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $customer->job_position }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg-3">
                <div class="form-group">
                    {!! Form::label('company_id', trans('customer.company'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $customer->company->name ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg-3">
                <div class="form-group">
                    {!! Form::label('mobile', trans('customer.mobile'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $customer->mobile }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('additional_info', trans('customer.address'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $customer->address }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">

                <div class="form-group">
                    <div class="controls">
                        @if (@$action == trans('action.show'))
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        @elseif (@$action == 'lost' || @$action == 'won')
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                            <button type="submit" class="btn btn-success"><i
                                        class="fa fa-check-square-o"></i> {{trans('table.ok')}}
                            </button>
                            {!! Form::close() !!}
                        @else
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}
                            </button>
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>