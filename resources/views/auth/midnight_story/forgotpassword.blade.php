@extends('auth.auth_layout')
@section('auth-page', 'forgotPassword')
@section('auth-template', 'midnight_story')
@section('title', t('Forgot Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'forgotPassword', 'authTheme' => 'midnight_story'])
@endsection
