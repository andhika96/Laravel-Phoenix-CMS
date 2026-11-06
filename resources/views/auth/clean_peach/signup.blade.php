@extends('auth.auth_layout')
@section('auth-page', 'signup')
@section('auth-template', 'clean_peach')
@section('title', t('Signup'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'signup', 'authTheme' => 'clean_peach'])
@endsection
