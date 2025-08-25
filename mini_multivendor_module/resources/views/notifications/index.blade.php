@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Notifications</h1>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif

  <form method="POST" action="{{ route('notifications.markRead') }}" class="mb-3">
    @csrf
    <button class="btn btn-sm btn-secondary">Mark all as read</button>
  </form>

  <h4 class="mt-4">Unread</h4>
  <ul class="list-group mb-4">
    @forelse($unread as $n)
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <a href="{{ route('notifications.open', $n->id) }}" class="text-decoration-none">
            {{ $n->data['message'] ?? 'Notification' }}
            @if(isset($n->data['product_name']))
              — <strong>{{ $n->data['product_name'] }}</strong>
              @if(isset($n->data['status'])) ({{ $n->data['status'] }}) @endif
            @endif
          </a>
          <div class="text-muted small">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <a href="{{ route('notifications.open', $n->id) }}" class="btn btn-sm btn-outline-primary">
          View
        </a>
      </li>
    @empty
      <li class="list-group-item">No unread notifications.</li>
    @endforelse
  </ul>

  <h4>Recent</h4>
  <ul class="list-group">
    @forelse($all as $n)
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <a href="{{ route('notifications.open', $n->id) }}" class="text-decoration-none">
            {{ $n->data['message'] ?? 'Notification' }}
            @if(isset($n->data['product_name']))
              — <strong>{{ $n->data['product_name'] }}</strong>
              @if(isset($n->data['status'])) ({{ $n->data['status'] }}) @endif
            @endif
          </a>
          <div class="text-muted small">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <a href="{{ route('notifications.open', $n->id) }}" class="btn btn-sm btn-outline-secondary">
          Open
        </a>
      </li>
    @empty
      <li class="list-group-item">No notifications yet.</li>
    @endforelse
  </ul>
</div>
@endsection
