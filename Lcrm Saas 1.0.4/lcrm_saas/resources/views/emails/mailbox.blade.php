@component('mail::message')
<div>
    {{ trans('emails.new_email_from') .' '. $userFrom->full_name }}
</div>
{{ trans('emails.subject') }} : {{ $subject }}
<h4>{{ trans('emails.message') }} :</h4>
{{$message}}
@if($role=='customer')
    @component('mail::button', ['url' => url('customers/mailbox#/m/inbox/'.$emails->id)])
        {{ trans('emails.check_here') }}
    @endcomponent
    @else
    @component('mail::button', ['url' => url('support#/s/tickets/'.$emails->id)])
        {{ trans('emails.check_here') }}
    @endcomponent
    @endif
@endcomponent
