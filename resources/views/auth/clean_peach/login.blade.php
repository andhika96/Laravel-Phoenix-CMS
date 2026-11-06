@extends('auth.auth_layout')
@section('auth-page', 'login')
@section('auth-template', 'clean_peach')
@section('title', t('Login'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'login', 'authTheme' => 'clean_peach'])
@endsection
