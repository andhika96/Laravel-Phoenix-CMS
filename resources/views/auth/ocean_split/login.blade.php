@extends('auth.auth_layout')
@section('auth-page', 'login')
@section('auth-template', 'ocean_split')
@section('title', t('Login'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'login', 'authTheme' => 'ocean_split'])
@endsection
