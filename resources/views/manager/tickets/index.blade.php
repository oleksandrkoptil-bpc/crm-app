@extends('layouts.manager', ['title' => 'Tickets'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tickets-list.css') }}">
@endpush

@section('content')
    <div class="header">
        <h1 class="title">Tickets</h1>
    </div>

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
