@extends('auth.auth_layout')
@section('auth-page', 'signup')
@section('auth-template', 'verdant_split')
@section('title', t('Signup'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'signup', 'authTheme' => 'verdant_split'])
@endsection
