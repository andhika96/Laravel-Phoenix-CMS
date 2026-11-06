@extends('auth.auth_layout')
@section('auth-page', 'resetPassword')
@section('auth-template', 'clean_peach')
@section('title', t('Reset Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'resetPassword', 'authTheme' => 'clean_peach'])
@endsection
