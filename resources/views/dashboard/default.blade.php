@extends('layouts.app')

@section('title', trans('default.default_dashboard'))

@section('contents')
    <dashboard-default></dashboard-default>
@endsection

<script>
    window.laravel = {
        name: @json(auth()->user()->first_name),
        date: @json(auth()->user()->created_at)
    }
</script>
