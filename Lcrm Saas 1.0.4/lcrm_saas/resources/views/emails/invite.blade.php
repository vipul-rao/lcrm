@component('mail::message')
# Hello
<p>{{ trans('emails.invitation_to_create_account') }}:</p>

@component('mail::button', ['url' => $inviteUrl])
{{ trans('emails.accept_invitation') }}
@endcomponent
@endcomponent
