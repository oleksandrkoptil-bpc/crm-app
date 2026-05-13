@extends('layouts.manager', ['title' => 'Ticket #'.$ticket->id])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ticket-item.css') }}">
@endpush

@section('content')
    <div class="header">
        <h1 class="title">Ticket #{{ $ticket->id }}</h1>
        <a class="muted" href="{{ route('manager.tickets.index') }}">Back to tickets</a>
    </div>

    <div class="panel">
        <div class="details">
            <div class="row">
                <div class="muted">Subject</div>
                <div>{{ $ticket->subject }}</div>
            </div>

            <div class="row">
                <div class="muted">Customer</div>
                <div>
                    {{ $ticket->customer->name }}
                    <div class="muted">{{ $ticket->customer->phone }}</div>
                    @if ($ticket->customer->email)
                        <div class="muted">{{ $ticket->customer->email }}</div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="muted">Message</div>
                <div class="message">{{ $ticket->message }}</div>
            </div>

            <div class="row">
                <div class="muted">Status</div>
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

            <div class="row">
                <div class="muted">Manager replied at</div>
                <div>{{ $ticket->manager_replied_at?->format('d.m.Y H:i') ?? '-' }}</div>
            </div>

            <div class="row">
                <div class="muted">Files</div>
                <div>
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
            </div>
        </div>
    </div>
@endsection
