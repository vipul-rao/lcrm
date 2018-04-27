@component('mail::message')
<div>
    {{ $subject }} <b>"{{ $subscription->name }}"</b>.
    @if($subscription->trial_ends_at!='')
        {{ trans('emails.your_trial_ends_at').' '. $subscription->trial_ends_at }}
    @endif
</div>
@endcomponent
