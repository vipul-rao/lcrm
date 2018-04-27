<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('option.category')}}</label>

            <div class="controls">
                @if (isset($option))
                    {{ $option->category }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('option.title')}}</label>

            <div class="controls">
                @if (isset($option))
                    {{ $option->title }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('option.value')}}</label>

            <div class="controls">
                @if (isset($option))
                    {{ $option->value }}
                @endif
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