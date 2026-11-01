{{-- ป้ายสถานะออเดอร์ (orders.status = pending / paid / shipped / completed / cancelled) --}}
@php
    $statusStyles = [
        'pending' => 'bg-[#F2F4F7] text-[#344054]',
        'paid' => 'bg-[#EEF0FB] text-[#3B4BB0]',
        'shipped' => 'bg-[#F4F3FF] text-[#5925DC]',
        'completed' => 'bg-[#ECFDF3] text-[#067647]',
        'cancelled' => 'bg-[#FEF3F2] text-[#B42318]',
    ];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $statusStyles[$status] ?? $statusStyles['pending'] }}" data-test="order-status">
    {{ __(ucfirst($status)) }}
</span>
