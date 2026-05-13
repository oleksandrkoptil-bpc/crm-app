<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller {

    public function index(): View {

        $tickets = Ticket::query()
            ->with('customer')
            ->latest()
            ->paginate(10);

        return view('manager.tickets.index', [
            'tickets' => $tickets,
            'statuses' => $this->statuses(),
        ]);
    }

    public function show(Ticket $ticket): View {

        $ticket->load('customer');

        return view('manager.tickets.show', [
            'ticket' => $ticket,
            'statuses' => $this->statuses(),
            'attachments' => $ticket->getMedia('attachments'),
        ]);
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse {

        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys($this->statuses()))],
        ]);

        $ticket->status = $data['status'];

        if ($ticket->status === Ticket::STATUS_PROCESSED && $ticket->manager_replied_at === null) {
            $ticket->manager_replied_at = now();
        }

        $ticket->save();

        return back()->with('status', 'Ticket status updated.');
    }

    public function download(Ticket $ticket, Media $media): BinaryFileResponse {

        abort_unless($media->model()->is($ticket), 404);
        abort_unless($media->collection_name === 'attachments', 404);

        return response()->download($media->getPath(), $media->file_name);
    }

    private function statuses(): array {
        return [
            Ticket::STATUS_NEW => 'New',
            Ticket::STATUS_IN_PROGRESS => 'In progress',
            Ticket::STATUS_PROCESSED => 'Processed',
        ];
    }
}
