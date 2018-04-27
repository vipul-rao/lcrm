<script src="{{ asset(mix('js/libs.js')) }}" type="text/javascript"></script>
<script src="{{ asset('js/pusher.min.js') }}"></script>
<script src="{{ asset('js/lobibox.min.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.min.js') }}"></script>
@if(!isset($no_vue))
<script src="{{asset(mix('js/secure.js')) }}" type="text/javascript"></script>
@endif
<script type="text/javascript" src="{{ asset('js/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/c3.min.js')}}"></script>