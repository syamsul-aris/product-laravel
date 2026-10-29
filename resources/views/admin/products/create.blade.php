@extends('admin.shared.layout')

@section('title', 'Create Product')

@section('content')
@include('admin.products.form', ['categories' => $categories])
@endsection
