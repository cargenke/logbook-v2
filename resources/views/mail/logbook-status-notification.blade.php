@component('mail::message')

Please find attached the Logbook with status - {{ $status }}.


Best Regards,

{{env('APP_NAME') }}
@endcomponent
