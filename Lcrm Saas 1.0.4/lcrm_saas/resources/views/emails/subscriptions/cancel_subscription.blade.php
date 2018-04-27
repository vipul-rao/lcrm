@component('mail::message')
<div>
    {{ trans('emails.your_subscription_with_plan') .' '. $subscription->name .' '.trans('emails.has_been_canceled').'.' }}
</div>
<div>
    @if(isset($subscription->ends_at))
        {{ trans('emails.continue_to_use_the_service_untill').' '. $subscription->ends_at }}.
    @endif
</div>
<div>
    {{ trans('emails.resume_your_subscription') }}.
</div>
@component('mail::button', ['url' => url('subscription')])
    {{ trans('emails.resume_subscription') }}
@endcomponent
@endcomponent