@extends('auth.auth_layout')
@section('auth-page', 'signup')
@section('auth-template', 'ocean_split')
@section('title', t('Signup'))
@section('content')
	@include('auth.modern.page', ['authPage' => 'signup', 'authTheme' => 'ocean_split'])
@endsection
