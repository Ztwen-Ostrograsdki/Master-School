@extends('errors::illustrated-layout')

@section('title', __('Page à accès limité'))
@section('code', '401')
@section('message', __($exception->getMessage() ?: 'Non authorisé'))
