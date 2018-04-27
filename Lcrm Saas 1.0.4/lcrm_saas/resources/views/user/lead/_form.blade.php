<div class="card">
    <div class="card-body">
        @if (isset($lead))
            {!! Form::model($lead, ['url' => $type . '/' . $lead->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('company_name') ? 'has-error' : '' }}">
                    {!! Form::label('company_name', trans('lead.company_name'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('company_name', null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('company_name', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group required {{ $errors->has('function') ? 'has-error' : '' }}">
                    {!! Form::label('function', trans('lead.function'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('function', isset($functions)?$functions:[0=>trans('lead.function')],null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('function', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group required {{ $errors->has('product_name') ? 'has-error' : '' }}">
                    {!! Form::label('product_name', trans('lead.product_name'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('product_name', null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('product_name', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group {{ $errors->has('company_site') ? 'has-error' : '' }}">
                        {!! Form::label('company_site', trans('lead.company_site'), ['class' => 'control-label required' ]) !!}
                        <div class="controls">
                            {!! Form::text('company_site', null, ['class' => 'form-control', 'placeholder'=>'Company Web Site']) !!}
                            <span class="help-block">{{ $errors->first('company_site', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group required {{ $errors->has('internal_notes') ? 'has-error' : '' }}">
                        {!! Form::label('internal_notes', trans('lead.additionl_info'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('internal_notes', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('internal_notes', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr/>
                </div>
                <div class="col-md-12">
                    <h4>{{ trans('lead.personal_info') }}:</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
                        {!! Form::label('title', trans('lead.title'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('title', $titles, null, ['id'=>'title', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group required {{ $errors->has('contact_name') ? 'has-error' : '' }}">
                        {!! Form::label('contact_name', trans('lead.contact_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('contact_name', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('contact_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group required {{ $errors->has('country_id') ? 'has-error' : '' }}">
                        {!! Form::label('country_id', trans('lead.country'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('country_id', $countries, null, ['id'=>'country_id', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('country_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group required {{ $errors->has('state_id') ? 'has-error' : '' }}">
                        {!! Form::label('state_id', trans('lead.state'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('state_id', isset($lead)?$states:[0=>trans('lead.select_state')], null, ['id'=>'state_id', 'class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('state_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group required {{ $errors->has('city_id') ? 'has-error' : '' }}">
                        {!! Form::label('city_id', trans('lead.city'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('city_id', isset($lead)?$cities:[0=>trans('lead.select_city')], null, ['id'=>'city_id', 'class' => 'form-control select2']) !!}
                            <span class="help-block">{{ $errors->first('city_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('address') ? 'has-error' : '' }}">
                        {!! Form::label('address', trans('lead.address'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('address', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('phone') ? 'has-error' : '' }}">
                        {!! Form::label('phone', trans('lead.phone'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('phone', null, ['class' => 'form-control','data-fv-integer' => "true"]) !!}
                            <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('mobile') ? 'has-error' : '' }}">
                        {!! Form::label('mobile', trans('lead.mobile'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('mobile', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                            <span class="help-block">{{ $errors->first('mobile', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::label('email', trans('lead.email'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::email('email', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        <div class="form-group required {{ $errors->has('priority') ? 'has-error' : '' }}">
            {!! Form::label('priority', trans('lead.priority'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::select('priority', $priority, null, ['id'=>'priority','class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('priority', ':message') }}</span>
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
        $(document).ready(function(){
            $("#country_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('company.select_country') }}"
            });
            $("#state_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('company.select_state') }}"
            });
            $("#city_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('company.select_city') }}"
            });
            $("#function").select2({
                theme: "bootstrap",
                placeholder: "{{ trans('lead.function') }}"
            });
            $("#title").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('lead.title') }}"
            });
            $("#priority").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('lead.priority') }}"
            });

        });
        $("#state_id").find("option:contains({{trans('company.select_state')}})").attr({
            selected: true,
            value: ""
        });
        $("#city_id").find("option:contains('{{trans('company.select_city')}}')").attr({
            selected: true,
            value: ""
        });
        $('#country_id').change(function () {
            getstates($(this).val());
        });
        @if(old('country_id'))
        getstates({{old('country_id')}});

        @endif
        function getstates(country) {
            $.ajax({
                type: "GET",
                url: '{{ url('lead/ajax_state_list')}}',
                data: {'id': country, _token: '{{ csrf_token() }}'},
                success: function (data) {
                    $('#state_id').empty();
                    $('#city_id').empty();
                    $('#state_id').select2({
                        theme: "bootstrap",
                        placeholder: "{{trans('company.select_state')}}"
                    }).trigger('change');
                    @if(old('state_id'))
                    getcities({{old('state_id')}});
                    @endif
                    $('#city_id').select2({
                        theme: "bootstrap",
                        placeholder: "{{trans('company.select_city')}}"
                    }).trigger('change');
                    $.each(data, function (val, text) {
                        $('#state_id').append($('<option></option>').val(val).html(text).attr('selected', val == "{{old('state_id')}}" ? true : false));
                    });
                }
            });
        }

        $('#state_id').change(function () {
            getcities($(this).val());
        });
        function getcities(cities) {
            $.ajax({
                type: "GET",
                url: '{{ url('lead/ajax_city_list')}}',
                data: {'id': cities, _token: '{{ csrf_token() }}'},
                success: function (data) {
                    $('#city_id').empty();
                    $('#city_id').select2({
                        theme: "bootstrap",
                        placeholder: "{{trans('company.select_city')}}"
                    }).trigger('change');
                    $.each(data, function (val, text) {
                        $('#city_id').append($('<option></option>').val(val).html(text).attr('selected', val == "{{old('city_id')}}" ? true : false));
                    });
                }
            });
        }
    </script>
@endsection
