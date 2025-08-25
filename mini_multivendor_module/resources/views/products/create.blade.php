@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Add Product</h1>
  <form method="POST" action="{{ route('products.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Price</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <button class="btn btn-primary">Submit for Approval</button>
  </form>
</div>
@endsection
