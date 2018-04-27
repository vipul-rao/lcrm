@component('mail::message')
# {{ trans('emails.subscription_extended') }}
{{ trans('emails.hello') }}, {{ trans('emails.your_subscription_for') }} {{config('app.name')}}  {{ trans('emails.is_extended_by').' '.$data['duration']}}
{{ trans('emails.below_reason') }}.
<hr>
{{$data['reason']}}

{{ trans('emails.thanks') }},<br>
{{ config('app.name') }}
@endcomponent
