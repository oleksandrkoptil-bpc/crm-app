<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller {

    public function index(Request $request): View {

        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->statuses()))],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $query = Ticket::query()->with('customer');

        $this->applyFilters($query, $filters);

        $tickets = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('manager.tickets.index', [
            'tickets' => $tickets,
            'statuses' => $this->statuses(),
            'filters' => $filters,
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

    private function applyFilters(Builder $query, array $filters): void {

        if (! empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['email'])) {
            $email = $filters['email'];

            $query->whereHas('customer', function (Builder $customerQuery) use ($email) {
                $customerQuery->where('email', 'like', "%{$email}%");
            });
        }

        if (! empty($filters['phone'])) {
            $phone = $filters['phone'];

            $query->whereHas('customer', function (Builder $customerQuery) use ($phone) {
                $customerQuery->where('phone', 'like', "%{$phone}%");
            });
        }
    }
}
