<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('contactus.name')}}</label>
                    <div class="controls">
                        {{ $contactUs->name }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('contactus.email')}}</label>
                    <div class="controls">
                        {{ $contactUs->email }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('contactus.phone_number')}}</label>
                    <div class="controls">
                        {{ $contactUs->phone_number }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('contactus.user')}}</label>
                    <div class="controls">
                        {{ isset($contactUs->user)?$contactUs->user->full_name:'New' }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('contactus.message')}}</label>
                    <div class="controls">
                        {{ $contactUs->message }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == trans('action.show'))
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @else
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}</button>
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @endif
            </div>
        </div>
    </div>
</div>