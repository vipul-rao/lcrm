@component('mail::message')
{{ trans('emails.your_plan_is_changed_to').' '. $subscription->name }}
@endcomponent
