@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Your Products</h1>
  <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add Product</a>
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  <table class="table table-bordered">
    <thead>
      <tr><th>Code</th><th>Name</th><th>Status</th><th>Price</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($products as $p)
      <tr>
        <td>{{ $p->code }}</td>
        <td>{{ $p->name }}</td>
        <td>{{ ucfirst($p->status) }}</td>
        <td>{{ $p->price }}</td>
        <td>
          <a class="btn btn-sm btn-secondary" href="{{ route('products.edit', $p) }}">Edit</a>
          <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline-block">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5">No products yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  {{ $products->links() }}
</div>
@endsection
