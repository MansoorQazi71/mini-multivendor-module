@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Pending Products</h1>
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  <table class="table table-bordered">
    <thead>
      <tr><th>Vendor</th><th>Code</th><th>Name</th><th>Price</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($products as $p)
      <tr>
        <td>{{ $p->user->name }}</td>
        <td>{{ $p->code }}</td>
        <td>{{ $p->name }}</td>
        <td>{{ $p->price }}</td>
        <td>
          <form method="POST" action="{{ route('admin.products.approve', $p) }}" style="display:inline">
            @csrf @method('PUT')
            <button class="btn btn-success btn-sm">Approve</button>
          </form>
          <form method="POST" action="{{ route('admin.products.reject', $p) }}" style="display:inline">
            @csrf @method('PUT')
            <button class="btn btn-danger btn-sm">Reject</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5">No pending products.</td></tr>
      @endforelse
    </tbody>
  </table>
  {{ $products->links() }}
</div>
@endsection
