@component('mail::message')
<div>
    {{ trans('emails.new_ticket_from').' '. $userFrom->full_name }}
</div>
{{ trans('emails.ticket') }} : {{ $subject }}
<div>
    <b>{{ trans('emails.message') }} :</b>
</div>
{{$message}}
@component('mail::button', ['url' => url('admin/support#/s/tickets/'.$tickets->id)])
    {{ trans('emails.check_here') }}
@endcomponent
@endcomponent
