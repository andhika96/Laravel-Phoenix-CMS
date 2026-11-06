@extends('auth.auth_layout')
@section('auth-page', 'forgotPassword')
@section('auth-template', 'verdant_split')
@section('title', t('Forgot Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'forgotPassword', 'authTheme' => 'verdant_split'])
@endsection
