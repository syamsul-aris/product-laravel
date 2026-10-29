@extends('admin.shared.layout')

@section('title', 'Edit Category')

@section('content')
@include('admin.categories.form', ['category' => $category])
@endsection
