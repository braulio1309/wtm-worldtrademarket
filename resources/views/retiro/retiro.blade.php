@extends('layouts.app')

@section('title', 'Retiros')

@section('contents')
    <retiro></retiro>
@endsection



<script>
    window.Laravel = {
        user: @json(auth()->user()->PaymentMethods)
    };
</script>
