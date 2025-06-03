@component('mail::message')

# Ödeme Hatırlatması

Sayın {{ $reminder->customer->name ?? 'Değerli Müşterimiz' }},

@if($reminder->due_date)
    @php
        $daysUntilDue = (int) $reminder->due_date->startOfDay()->diffInDays(now()->startOfDay(), false);
    @endphp
    @if($reminder->due_date->isToday())
        Bugün ({{ $reminder->due_date->format('d.m.Y') }}) {{ number_format($debt->amount, 2, ',', '.') }} ₺ tutarında bir borcunuz bulunmaktadır.
    @elseif($reminder->due_date->isPast())
        {{ abs($daysUntilDue) }} gün önce ({{ $reminder->due_date->format('d.m.Y') }}) vadesi geçmiş {{ number_format($debt->amount, 2, ',', '.') }} ₺ tutarında bir borcunuz bulunmaktadır.
    @else
        {{ $daysUntilDue }} gün sonra ({{ $reminder->due_date->format('d.m.Y') }}) {{ number_format($debt->amount, 2, ',', '.') }} ₺ tutarında bir borcunuz bulunmaktadır.
    @endif
@else
    Ödeme tarihiniz belirtilmemiş. {{ number_format($debt->amount, 2, ',', '.') }} ₺ tutarında borcunuz bulunmaktadır.
@endif

@if($reminder->notes)
**Not:** {{ $reminder->notes }}
@endif

Ödemenizi en kısa sürede gerçekleştirmenizi rica ederiz.

Bizi tercih ettiğiniz için teşekkür ederiz,

@endcomponent
