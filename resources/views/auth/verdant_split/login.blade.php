@extends('auth.auth_layout')
@section('auth-page', 'login')
@section('auth-template', 'verdant_split')
@section('title', t('Login'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'login', 'authTheme' => 'verdant_split'])
@endsection
