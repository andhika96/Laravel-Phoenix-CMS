@extends('auth.auth_layout')
@section('auth-page', 'forgotPassword')
@section('auth-template', 'ocean_split')
@section('title', t('Forgot Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'forgotPassword', 'authTheme' => 'ocean_split'])
@endsection
