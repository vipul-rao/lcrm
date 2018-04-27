<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 col-md-3 m-t-20">
                <label class="control-label" for="title">{{trans('email_template.title')}}</label>
                <div class="controls">
                    {{ $emailTemplate->title }}
                </div>
            </div>
            <div class="col-sm-6 col-md-3 m-t-20">
                <label class="control-label" for="title">{{trans('email_template.text')}}</label>
                <div class="controls">
                    {{ $emailTemplate->text }}
                </div>
            </div>
        </div>
        <div class="form-group m-t-20">
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