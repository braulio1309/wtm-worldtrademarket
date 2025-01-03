@extends('layouts.app')

@section('title', 'Transacciones')

@section('contents')
    <balance></balance>
@endsection

<script>
    window.Laravel = {
        user: @json(auth()->user()->account->balance)
    };
</script>

