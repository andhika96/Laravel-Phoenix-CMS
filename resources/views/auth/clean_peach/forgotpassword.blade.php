@extends('auth.auth_layout')
@section('auth-page', 'forgotPassword')
@section('auth-template', 'clean_peach')
@section('title', t('Forgot Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'forgotPassword', 'authTheme' => 'clean_peach'])
@endsection
