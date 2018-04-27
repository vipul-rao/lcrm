@component('mail::message')
{{ trans('emails.your_card_is_successfully_updated_to').' '. $organization->card_brand.' with No ****'.$organization->card_last_four }}
@endcomponent
