@component('mail::message')
<div>
    {{ trans('emails.you_have_reply_from').' '. $userFrom->full_name }}
</div>
{{ trans('emails.ticket') }} : {{ $subject }}
<h4>{{ trans('emails.message') }} :</h4>
{{$message}}
@if($userFrom->inRole('admin'))
    @component('mail::button', ['url' => url('support#/s/tickets/'.$tickets->id)])
        {{ trans('emails.check_here') }}
    @endcomponent
@else
@component('mail::button', ['url' => url('admin/support#/s/tickets/'.$tickets->id)])
    {{ trans('emails.check_here') }}
@endcomponent
@endif
@endcomponent
