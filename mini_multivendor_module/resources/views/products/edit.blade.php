@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Edit Product</h1>
  <form method="POST" action="{{ route('products.update', $product) }}">
    @csrf @method('PUT')
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control">{{ $product->description }}</textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Price</label>
      <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
    </div>
    <button class="btn btn-primary">Update</button>
  </form>
</div>
@endsection
