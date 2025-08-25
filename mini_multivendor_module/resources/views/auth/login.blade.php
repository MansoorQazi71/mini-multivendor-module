@extends('layouts.app')

@section('content')
<div class="container" style="max-width:480px;">
  <h1 class="mb-4">Login</h1>
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif
  <form method="POST" action="{{ route('login.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>
@endsection
