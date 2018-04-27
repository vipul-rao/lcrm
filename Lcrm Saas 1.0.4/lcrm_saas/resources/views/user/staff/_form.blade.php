<div class="card">
    <div class="card-body">
        @if (isset($staff))
            {!! Form::model($staff, ['url' => $type . '/' . $staff->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('user_avatar_file') ? 'has-error' : '' }}">
                    {!! Form::label('user_avatar_file', trans('staff.user_avatar'), ['class' => 'control-label']) !!}
                    <div class="row">
                        @if(isset($staff->user_avatar))
                            <image-upload name="user_avatar_file" old-image="{{ url('uploads/avatar/thumb_'.$staff->user_avatar) }}"></image-upload>
                        @else
                            <image-upload name="user_avatar_file"></image-upload>
                        @endif
                    </div>
                    <span class="help-block">{{ $errors->first('user_avatar_file', ':message') }}</span>
                </div>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('first_name') ? 'has-error' : '' }}">
            {!! Form::label('first_name', trans('staff.first_name'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('last_name') ? 'has-error' : '' }}">
            {!! Form::label('last_name', trans('staff.last_name'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
            {!! Form::label('phone_number', trans('staff.phone_number'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('phone_number', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label('email', trans('staff.email'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('password') ? 'has-error' : '' }}">
            {!! Form::label('password', trans('staff.password'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::password('password', ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('password', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
            {!! Form::label('password_confirmation', trans('staff.password_confirmation'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel-content">
                    <h4>{{trans('staff.permissions')}}</h4>
                    <div class="row">
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.sales_teams')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_team.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['sales_team.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_team.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['sales_team.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_team.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['sales_team.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.leads')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="leads.read" class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['leads.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="leads.write" class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['leads.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="leads.delete" class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['leads.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.opportunities')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="opportunities.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['opportunities.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="opportunities.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['opportunities.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="opportunities.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['opportunities.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.logged_calls')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="logged_calls.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['logged_calls.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="logged_calls.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['logged_calls.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="logged_calls.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['logged_calls.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.meetings')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="meetings.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['meetings.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="meetings.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['meetings.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="meetings.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['meetings.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.products')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="products.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['products.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="products.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['products.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="products.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['products.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.quotations')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="quotations.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['quotations.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="quotations.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['quotations.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="quotations.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['quotations.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.sales_orders')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_orders.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['sales_orders.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_orders.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['sales_orders.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="sales_orders.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['sales_orders.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.invoices')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="invoices.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['invoices.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="invoices.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['invoices.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="invoices.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['invoices.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.staff')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="staff.read" class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['staff.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="staff.write" class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['staff.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="staff.delete" class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['staff.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-lg-2 m-b-10">
                            <p><strong>{{trans('staff.customers')}}</strong></p>
                            <div class="input-group">
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="customers.read"
                                           class='icheckgreen'
                                           @if(isset($staff) && $staff->hasAccess(['customers.read'])) checked @endif>
                                    {{trans('staff.read')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="customers.write"
                                           class='icheckblue'
                                           @if(isset($staff) && $staff->hasAccess(['customers.write'])) checked @endif>
                                    {{trans('staff.write')}}
                                </label>
                                <label class="w-100">
                                    <input type="checkbox" name="permissions[]" value="customers.delete"
                                           class='icheckred'
                                           @if(isset($staff) && $staff->hasAccess(['customers.delete'])) checked @endif>
                                    {{trans('staff.delete')}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>


@section('scripts')
    <script>
        $(document).ready(function () {
            $('.icheckgreen').iCheck({
                checkboxClass: 'icheckbox_minimal-green',
                radioClass: 'iradio_minimal-green'
            });
            $('.icheckblue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('.icheckred').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });

            $("input[value$='write'],input[value$='delete']").on('ifChecked', function(){
                var item = $(this).val();
                var part = item.split('.');
                $("input[value='"+part[0]+".read']").iCheck('check').iCheck('disable');
            });
            $("input[value$='write'],input[value$='delete']").on('ifUnchecked', function(){
                var item = $(this).val();
                var part = item.split('.');
                if(!$("input[value='" + part[0] + ".write']").is(":checked") && !$("input[value='" + part[0] + ".delete']").is(":checked")) {
                    $("input[value='" + part[0] + ".read']").iCheck('enable').iCheck('uncheck');
                }
            });
            $(".btn-success").click(function()
            {
                $("input").iCheck('enable');
            });
            $("input[type='checkbox']:checked").each(function () {
                var item = $(this).val();
                var part = item.split('.');
                if($("input[value='" + part[0] + ".write']").is(":checked") || $("input[value='" + part[0] + ".delete']").is(":checked")) {
                    $("input[value='" + part[0] + ".read']").iCheck('check').iCheck('disable');
                }
            });
        });
    </script>
@stop
