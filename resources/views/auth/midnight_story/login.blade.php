@extends('auth.auth_layout')
@section('auth-page', 'login')
@section('auth-template', 'midnight_story')
@section('title', t('Login'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'login', 'authTheme' => 'midnight_story'])
@endsection
