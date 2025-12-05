@extends('Core::email.layout')
@section('body')
Email: {{ $options['user']->email_address }}
@endsection