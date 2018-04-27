@component('mail::message')

<div> {{ trans('emails.welcome').' '. $name }}, </div>
<div>{{ trans('emails.you_have_been_registered_to') }} <a href="{{url('/')}}">{{ $site_name }}</a>.</div>
@component('mail::button', ['url' => url('/')])
    {{ trans('emails.visit_site') }}
@endcomponent
@endcomponent
