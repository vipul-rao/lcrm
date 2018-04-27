<div class="card">
    <div class="card-body">
        <div class="form-group">
            <div class="row">
                <div class="col-12">
                    <div class="fileinput fileinput-new">
                        <div class="fileinput-preview thumbnail form_Blade">
                            @if(isset($organization->logo))
                                <img src="{{ url('uploads/organizations/thumb_'.$organization->logo) }}" alt="avatar">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
       <div class="row">
           <div class="col-md-4">
               <div class="form-group">
                   <label class="control-label" for="title">{{trans('organizations.name')}}</label>
                   <div class="controls">
                       {{ $organization->name }}
                   </div>
               </div>
           </div>
           <div class="col-md-4">
               <div class="form-group">
                   <label class="control-label" for="title">{{trans('organizations.email')}}</label>
                   <div class="controls">
                       {{ $organization->email }}
                   </div>
               </div>
           </div>
           <div class="col-md-4">
               <div class="form-group">
                   <label class="control-label" for="title">{{trans('organizations.phone_number')}}</label>
                   <div class="controls">
                       {{ $organization->phone_number }}
                   </div>
               </div>
           </div>
       </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('organizations.users')}}</label>
                    <div class="controls">
                        {{ isset($organization->staffWithUser)?$organization->staffWithUser->count():0 }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                  <label class="control-label" for="title">{{trans('organizations.companies')}}</label>
                  <div class="controls">
                      {{ isset($organization->companies)?$organization->companies->count():0 }}
                  </div>
              </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == trans('action.show'))
                <a href="{{ url($type) }}" class="btn btn-warning">
                    <i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @else
                <button type="submit" class="btn btn-danger"><i class="fa fa-ban"></i> {{trans('table.disable')}}</button>
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @endif
            </div>
        </div>
    </div>
</div>