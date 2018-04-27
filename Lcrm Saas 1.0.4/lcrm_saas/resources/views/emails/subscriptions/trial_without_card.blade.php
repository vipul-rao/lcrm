@component('mail::message')

<div>
    {{ $subject }},
</div>
<div>
    {{ trans('emails.your_trial_ends_at').' '. $organization->trial_ends }}
</div>
@endcomponent
