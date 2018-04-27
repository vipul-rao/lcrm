@component('mail::message')

<div> <b>{{ trans('emails.subject') }}</b> : {{ $subject }} </div>
<div> <b>{{ trans('contactus.name') }}</b> : {{ $name }} </div>
<div> <b> {{ trans('contactus.phone_number') }} </b> : {{ $phone_number }}</div>
<div> <b> {{ trans('contactus.message') }} </b> : </div>
<div> {{ $message }} </div>
@endcomponent
