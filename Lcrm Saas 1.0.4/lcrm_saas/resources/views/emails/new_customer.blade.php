@component('mail::message')
# {{ trans('emails.hello') }}

{{ trans('emails.your_login_details') .' '.$sitename}}

<b>{{ trans('emails.email') }}:</b> {{$email}}

<b>{{ trans('emails.password') }}:</b> {{$password}}

@component('mail::button', ['url' => url('/')])
{{ trans('emails.click_here_to_login') }}
@endcomponent

{{ trans('emails.thanks') }},<br>
{{ config('app.name') }}
@endcomponent
