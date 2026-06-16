@extends('layouts.admin') {{-- Ganti dengan nama layout admin kamu --}}

@section('content')
    <livewire:admin.user-hydration-table />
    
    {{-- Jika kamu menggunakan Livewire v3, scripts & styles sudah otomatis ter-inject --}}
@endsection