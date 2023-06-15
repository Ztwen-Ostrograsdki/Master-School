@extends('errors::illustrated-layout')
@section('title', __('NOT FOUND'))
@section('code', '404')
@section('message', __($exception->getMessage() ?: 'Page introuvable'))
@section('image')
<img width="500" height="500" src="/myassets/errors/nf6.png" alt="">
@endsection