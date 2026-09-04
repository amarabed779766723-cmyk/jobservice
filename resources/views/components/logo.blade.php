@props(['type' => 'full', 'class' => ''])

@if($type === 'full')
    {{-- الشعار الكامل --}}
    <img src="{{ asset('assets/branding/logo-full.png') }}" 
         alt="JOB SERVICE" 
         class="{{ $class }}"
         style="height: 40px; width: auto;">
@elseif($type === 'transparent')
    {{-- شعار شفاف --}}
    <img src="{{ asset('assets/branding/logo-full-transparent.png') }}" 
         alt="JOB SERVICE" 
         class="{{ $class }}"
         style="height: 40px; width: auto;">
@elseif($type === 'mark')
    {{-- رمز JS فقط --}}
    <img src="{{ asset('assets/branding/logo-mark.png') }}" 
         alt="JOB SERVICE" 
         class="{{ $class }}"
         style="height: 40px; width: 40px; border-radius: 8px;">
@elseif($type === 'icon')
    {{-- أيقونة التطبيق --}}
    <img src="{{ asset('assets/branding/app-icon.png') }}" 
         alt="JOB SERVICE" 
         class="{{ $class }}"
         style="height: 40px; width: 40px; border-radius: 8px;">
@endif