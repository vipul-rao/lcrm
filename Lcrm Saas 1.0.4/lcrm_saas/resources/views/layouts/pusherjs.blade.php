<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
        cluster: 'ap2',
        encrypted: true
    });

    //    Cancel Subscription
    var cancelSubscriptionChannel = pusher.subscribe('{{ 'subscription_canceled_channel'.$user->id }}');
    cancelSubscriptionChannel.bind('{{ 'subscription_canceled_event'.$user->id }}', function(data) {
        Lobibox.notify('info', {
            delay: 10000,
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            rounded: true,
            icon: false,
            title: 'Subscription Cancelled',
            msg: data.message,
            onClickUrl: '{{ url('subscription') }}'
        });
    });

    //    resume Subscription
    var resumeSubscriptionChannel = pusher.subscribe('{{ 'resume_subscription_channel'.$user->id }}');
    resumeSubscriptionChannel.bind('{{ 'resume_subscription_event'.$user->id }}', function(data) {
        Lobibox.notify('info', {
            delay: 10000,
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            rounded: true,
            icon: false,
            title: 'Resume Subscription',
            msg: data.message,
            onClickUrl: '{{ url('subscription') }}'
        });
    });

    //    change plan
    var changePlanChannel = pusher.subscribe('{{ 'change_plan_channel'.$user->id }}');
    changePlanChannel.bind('{{ 'change_plan_event'.$user->id }}', function(data) {
        Lobibox.notify('info', {
            delay: 10000,
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            rounded: true,
            icon: false,
            title: 'Change Plan',
            msg: data.message,
            onClickUrl: '{{ url('subscription') }}'
        });
    });

    //    extend subscription
    var extendSubscriptionChannel = pusher.subscribe('{{ 'extend_subscription_channel'.$user->id }}');
    extendSubscriptionChannel.bind('{{ 'extend_subscription_event'.$user->id }}', function(data) {
        Lobibox.notify('info', {
            delay: 10000,
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            rounded: true,
            icon: false,
            title: 'Extend Subscription',
            msg: data.message,
            onClickUrl: '{{ url('subscription') }}'
        });
    });
</script>