@extends('admin.shared.layout')

@section('title', 'Edit Product')

@section('content')
@include('admin.products.form', ['product' => $product, 'categories' => $categories])
@endsection
