@extends('auth.auth_layout')
@section('auth-page', 'signup')
@section('auth-template', 'midnight_story')
@section('title', t('Signup'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'signup', 'authTheme' => 'midnight_story'])
@endsection
