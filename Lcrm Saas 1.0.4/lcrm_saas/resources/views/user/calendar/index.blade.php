@extends('layouts.user')

@section('title')
    {{ $title }}
@stop

@section('content')

    <div id="calendar" class="bg-white"></div>
    <div id="fullCalModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modalTitle" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span> <span class="sr-only">close</span></button>
                </div>
                <div id="modalBody" class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{trans('table.close')}}</button>
                </div>
            </div>
        </div>
    </div>

@stop
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                "header": {
                    "left": "prev,next today",
                    "center": "title",
                    "right": "month,agendaWeek,agendaDay"
                },
                "eventLimit": true,
                "firstDay": 1,
                "eventClick": function(event){
                    $('#modalTitle').html(event.title);
                    $('#modalBody').html(event.description);
                    $('#fullCalModal').modal();
                },
                "eventSources": [
                    {
                        url:"{{url('calendar/events')}}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        error: function() {
                            alert('there was an error while fetching events!');
                        }
                    }
                ]
            });
        });
    </script>
@stop
