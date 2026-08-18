@component('mail::message')
# New Logbook Request

A new logbook transfer request has been created.

| Field | Value |
| :--- | :--- |
| Chassis number | {{ $logbookRequest->chasisNumber ?? 'N/A' }} |
| Registration number | {{ $logbookRequest->regNumber ?? 'N/A' }} |
| First owner | {{ $logbookRequest->name1 ?? 'N/A' }} |
| First owner email | {{ $logbookRequest->email ?? 'N/A' }} |
| Phone number 1 | {{ $logbookRequest->tel1 ?? 'N/A' }} |
| Phone number 2 | {{ $logbookRequest->tel2 ?? 'N/A' }} |
| KRA PIN 1 | {{ $logbookRequest->PinNo1 ?? 'N/A' }} |
| Other owner | {{ $logbookRequest->name2 ?? 'N/A' }} |
| KRA PIN 2 | {{ $logbookRequest->PinNo2 ?? 'N/A' }} |
| KRA PIN 3 | {{ $logbookRequest->PinNo3 ?? 'N/A' }} |
| Created at | {{ $logbookRequest->createdOn ?? $logbookRequest->created_at ?? 'N/A' }} |

Regards,<br>
{{ config('app.name') }}
@endcomponent