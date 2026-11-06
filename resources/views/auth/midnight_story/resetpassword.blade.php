@extends('auth.auth_layout')
@section('auth-page', 'resetPassword')
@section('auth-template', 'midnight_story')
@section('title', t('Reset Password'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'resetPassword', 'authTheme' => 'midnight_story'])
@endsection
