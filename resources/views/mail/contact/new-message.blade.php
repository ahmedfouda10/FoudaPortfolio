<x-mail::message>
# {{ __('mail.new_contact_message.heading') }}

<x-mail::table>
| | |
|:--|:--|
| **{{ __('mail.new_contact_message.from') }}** | {{ $contactMessage->name }} &lt;{{ $contactMessage->email }}&gt; |
| **{{ __('mail.new_contact_message.type') }}** | {{ $contactMessage->inquiry_type->getLabel() }} |
@if (filled($contactMessage->budget))
| **{{ __('mail.new_contact_message.budget') }}** | {{ $contactMessage->budget }} |
@endif
| **{{ __('mail.new_contact_message.subject_label') }}** | {{ $contactMessage->subject }} |
</x-mail::table>

{{-- One escaped HTML line, so nothing the visitor typed is interpreted as Markdown. --}}
<x-mail::panel>
<div>{!! str_replace(["\r\n", "\r", "\n"], '<br>', e($contactMessage->message)) !!}</div>
</x-mail::panel>

<x-mail::button :url="$url">
{{ __('mail.new_contact_message.open') }}
</x-mail::button>

{{ __('mail.new_contact_message.reply_hint') }}
</x-mail::message>
