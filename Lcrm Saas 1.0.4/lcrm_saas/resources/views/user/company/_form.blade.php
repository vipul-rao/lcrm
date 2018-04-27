<div class="card">
    <div class="card-body">
        @if (isset($company))
            {!! Form::model($company, ['url' => $type . '/' . $company->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
            <div class="row">
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('company_avatar_file') ? 'has-error' : '' }}">
                        {!! Form::label('company_avatar_file', trans('company.company_avatar'), ['class' => 'control-label']) !!}
                        <div class="controls row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="row">
                                    @if(isset($company->company_avatar))
                                        <image-upload name="company_avatar_file" old-image="{{ url('uploads/company/thumb_'.$company->company_avatar) }}"></image-upload>
                                    @else
                                        <image-upload name="company_avatar_file"></image-upload>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <span class="help-block">{{ $errors->first('company_avatar_file', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                        {!! Form::label('name', trans('company.company_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('website') ? 'has-error' : '' }}">
                        {!! Form::label('website', trans('company.website'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('website', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('website', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('phone') ? 'has-error' : '' }}">
                        {!! Form::label('phone', trans('company.phone'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('phone', null, ['class' => 'form-control','data-fv-integer' => "true"]) !!}
                            <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('mobile') ? 'has-error' : '' }}">
                        {!! Form::label('mobile', trans('company.mobile'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('mobile', null, ['class' => 'form-control','data-fv-integer' => "true"]) !!}
                            <span class="help-block">{{ $errors->first('mobile', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::label('email', trans('company.email'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::email('email', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('fax') ? 'has-error' : '' }}">
                        {!! Form::label('fax', trans('company.fax'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('fax', null, ['class' => 'form-control','data-fv-integer' => "true"]) !!}
                            <span class="help-block">{{ $errors->first('fax', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('country_id') ? 'has-error' : '' }}">
                        {!! Form::label('country_id', trans('company.country'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('country_id', $countries, null, ['id'=>'country_id', 'class' => 'form-control select2']) !!}
                            <span class="help-block">{{ $errors->first('country_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('state_id') ? 'has-error' : '' }}">
                        {!! Form::label('state_id', trans('company.state'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('state_id', isset($company)?$states:[0=>trans('company.select_state')], null, ['id'=>'state_id', 'class' => 'form-control select2']) !!}
                            <span class="help-block">{{ $errors->first('state_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('city_id') ? 'has-error' : '' }}">
                        {!! Form::label('city_id', trans('company.city'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('city_id', isset($company)?$cities:[0=>trans('company.select_city')], null, ['id'=>'city_id', 'class' => 'form-control select2']) !!}
                            <span class="help-block">{{ $errors->first('city_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('address') ? 'has-error' : '' }}">
                        {!! Form::label('address', trans('company.address'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::textarea('address', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    {!! Form::hidden('latitude', null, ['class' => 'form-control', 'id'=>"latitude"]) !!}
                    {!! Form::hidden('longitude', null, ['class' => 'form-control', 'id'=>"longitude"]) !!}
                    @if(isset($company))
                        <div class="form-group {{ $errors->has('main_contact_person') ? 'has-error' : '' }}">
                            {!! Form::label('main_contact_person', trans('company.main_contact_person'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::select('main_contact_person', isset($customers)?$customers:[''=>trans('company.main_contact_person')], null, ['id'=>'main_contact_person', 'class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('main_contact_person', ':message') }}</span>
                            </div>
                        </div>
                        @endif
                </div>
            </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i
                            class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i
                            class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>

@section('scripts')
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('services.gmaps_key')}}&libraries=places"></script>
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
            @if(isset($company->main_contact_person))
                $("#main_contact_person").select2({
                    theme:"bootstrap",
                    placeholder:"{{ trans('company.main_contact_person') }}"
                });
            @else
                $("#main_contact_person").select2({
                    theme:"bootstrap",
                    placeholder:"{{ trans('company.main_contact_person') }}"
                }).prepend('<option selected value="">{{trans('company.main_contact_person')}}</option>');
            @endif
            $("#sales_team_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('company.sales_team_id') }}"
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
        $('#city_id').change(function () {
            var geocoder = new google.maps.Geocoder();
            if (typeof $('#city_id').select2('data')[0] !== "undefined" && typeof $('#state_id').select2('data')[0] !== "undefined") {
                geocoder.geocode({'address': '"' + $('#city_id').select2('data')[0].text + '",' + $('#state_id').select2('data')[0].text + '"'}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        $('#latitude').val(results[0].geometry.location.lat());
                        $('#longitude').val(results[0].geometry.location.lng());
                    }
                });
            }
        })
    </script>
@endsection
