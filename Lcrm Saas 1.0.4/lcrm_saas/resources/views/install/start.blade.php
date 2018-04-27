@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => ['welcome' => 'selected']])
   <div class="content">
       <div class="card">
           <div class="card-header bg-white">
               <h4>{{ trans('install.welcome') }}</h4>
           </div>
           <div class="card-body">
               <div class="m-t-20">
                   <p>{{trans('install.steps_guide')}}</p>
                   <p>{{trans('install.installation_process')}} </p>
               </div>
               <div class="m-t-10">
                   <a href="{{ url('install/requirements') }}">
                       <button class="btn btn-primary pull-right" type="button">
                           {{trans('install.next')}}
                           <i class="fa fa-arrow-right start_file-icon"></i>
                       </button>
                   </a>
               </div>
           </div>
       </div>
   </div>
@stop
