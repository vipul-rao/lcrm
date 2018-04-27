<meta charset="UTF-8">
<title>
    {{$title or 'LCRM'}} | {{ config('app.name') }}
</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

<meta id="token" name="token" value="{{ csrf_token() }}"> @if(Sentinel::check())
    <meta id="pusherKey" name="pusherKey" value="{{ isset($settings['pusher_public'])?$settings['pusher_public']:'' }}">
@endif
