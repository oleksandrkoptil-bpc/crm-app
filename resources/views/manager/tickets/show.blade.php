@extends('layouts.manager', ['title' => 'Ticket #'.$ticket->id])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ticket-item.css') }}">
@endpush

@section('content')
    <div class="header">
        <h1 class="title">Ticket #{{ $ticket->id }}</h1>
        <a class="muted" href="{{ route('manager.tickets.index') }}">Back to tickets</a>
    </div>

    <div class="ticket-page">
        <section class="ticket-main panel">
            <div class="ticket-section">
                <div class="section-label">Subject</div>
                <h2 class="ticket-subject">{{ $ticket->subject }}</h2>
            </div>

            <div class="ticket-section">
                <div class="section-label">Customer</div>
                <div class="customer-card">
                    <div class="customer-name">{{ $ticket->customer->name }}</div>
                    <div class="customer-meta">{{ $ticket->customer->phone }}</div>
                    @if ($ticket->customer->email)
                        <div class="customer-meta">{{ $ticket->customer->email }}</div>
                    @endif
                </div>
            </div>

            <div class="ticket-section">
                <div class="section-label">Message</div>
                <div class="message-box">{{ $ticket->message }}</div>
            </div>

            <div class="ticket-section">
                <div class="section-label">Files</div>
                @if ($attachments->isNotEmpty())
                    <ul class="files">
                        @foreach ($attachments as $media)
                            <li>
                                <a href="{{ route('manager.tickets.media.download', [$ticket, $media]) }}">
                                    {{ $media->file_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="muted">No files attached.</span>
                @endif
            </div>
        </section>

        <aside class="ticket-sidebar">
            <div class="panel sidebar-panel">
                <div class="sidebar-title">Status</div>

                <form class="status-form" method="post" action="{{ route('manager.tickets.update-status', $ticket) }}">
                    @csrf
                    @method('patch')

                    <select class="select" name="status">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button class="button" type="submit">Save</button>

                    @error('status')
                        <div class="muted">{{ $message }}</div>
                    @enderror
                </form>
            </div>

            <div class="panel sidebar-panel">
                <div class="sidebar-title">Dates</div>

                <div class="date-row">
                    <span>Created</span>
                    <strong>{{ $ticket->created_at->format('d.m.Y H:i') }}</strong>
                </div>

                <div class="date-row">
                    <span>Updated</span>
                    <strong>{{ $ticket->updated_at->format('d.m.Y H:i') }}</strong>
                </div>

                <div class="date-row">
                    <span>Manager replied</span>
                    <strong>{{ $ticket->manager_replied_at?->format('d.m.Y H:i') ?? '-' }}</strong>
                </div>
            </div>
        </aside>
    </div>
@endsection
