@extends('auth.auth_layout')
@section('auth-page', 'resetPassword')
@section('auth-template', 'ocean_split')
@section('title', t('Reset Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'resetPassword', 'authTheme' => 'ocean_split'])
@endsection
