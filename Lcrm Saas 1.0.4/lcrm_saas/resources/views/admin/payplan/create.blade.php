@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <!-- ./ notifications -->
    @include($type.'/_form')
@stop

@section('scripts')
    <script>
    </script>
@endsection
