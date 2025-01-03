@extends('layouts.app')

@section('title', trans('default.profile'))

@section('contents')
    <my-profile></my-profile>
@endsection

<script>
    window.Laravel = {
        id: @json(encrypt(auth()->user()->id))
    };
</script>
