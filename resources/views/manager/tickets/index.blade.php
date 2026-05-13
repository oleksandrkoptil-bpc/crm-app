@extends('layouts.manager', ['title' => 'Tickets'])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tickets-list.css') }}">
@endpush

@section('content')
<div class="header">
    <h1 class="title">Tickets</h1>
</div>

<form class="filters panel" method="get" action="{{ route('manager.tickets.index') }}">
    <div class="filter-field">
        <label for="date">Date</label>
        <input id="date" type="date" name="date" value="{{ $filters['date'] ?? '' }}">
    </div>

    <div class="filter-field">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" @selected(($filters['status'] ?? '' )===$value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-field">
        <label for="email">Email</label>
        <input id="email" type="text" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="customer@email.com">
    </div>

    <div class="filter-field">
        <label for="phone">Phone</label>
        <input id="phone" type="text" name="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="+380...">
    </div>

    <div class="filter-actions">
        <button class="button" type="submit">Filter</button>
        <a class="clear-link" href="{{ route('manager.tickets.index') }}">Clear</a>
    </div>
</form>

<div class="panel">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
            <tr>
                <td>#{{ $ticket->id }}</td>
                <td>
                    <a href="{{ route('manager.tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                </td>
                <td>
                    {{ $ticket->customer->name }}
                    <div class="muted">{{ $ticket->customer->phone }}</div>
                    <div class="muted">{{ $ticket->customer->email }}</div>
                </td>
                <td>
                    <span class="status status-{{ $ticket->status }}">
                        {{ $statuses[$ticket->status] ?? $ticket->status }}
                    </span>
                </td>
                <td class="muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="muted">No tickets yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $tickets->links() }}
</div>
@endsection
