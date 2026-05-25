<?php

namespace App\Http\Controllers;

use App\Mail\TicketResolvedMail;
use App\Models\Crop;
use App\Models\Prediction;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WeatherLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    // Admin overview
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_predictions' => Prediction::count(),
            'total_crops' => Crop::count(),
            'total_weather_logs' => WeatherLog::count(),
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
        ];

        $recentPredictions = Prediction::with(['crop', 'user'])->latest()->limit(10)->get();
        $recentUsers = User::latest()->limit(5)->get();

        $cropPredictions = Prediction::with('crop')->get()->groupBy(fn ($p) => $p->crop->name ?? 'Unknown')->map->count();

        $riskData = [
            'Low' => Prediction::where('risk_level', 'Low')->count(),
            'Medium' => Prediction::where('risk_level', 'Medium')->count(),
            'High' => Prediction::where('risk_level', 'High')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentPredictions', 'recentUsers', 'cropPredictions', 'riskData'));
    }

    // Crop CRUD
    public function cropIndex()
    {
        $crops = Crop::withCount('predictions')->latest()->paginate(15);

        return view('admin.crops.index', compact('crops'));
    }

    public function cropCreate()
    {
        return view('admin.crops.create');
    }

    public function cropStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:crops,name',
            'min_temp' => 'required|numeric',
            'max_temp' => 'required|numeric|gt:min_temp',
            'min_rainfall' => 'required|numeric|min:0',
            'max_rainfall' => 'required|numeric|gt:min_rainfall',
            'min_humidity' => 'required|numeric|between:0,100',
            'max_humidity' => 'required|numeric|between:0,100|gt:min_humidity',
            'base_yield' => 'required|numeric|min:0.1',
        ]);

        Crop::create($validated);

        return redirect()->route('admin.crops')->with('success', "Crop '{$validated['name']}' added successfully.");
    }

    public function cropEdit(Crop $crop)
    {
        return view('admin.crops.edit', compact('crop'));
    }

    public function cropUpdate(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:crops,name,'.$crop->id,
            'min_temp' => 'required|numeric',
            'max_temp' => 'required|numeric|gt:min_temp',
            'min_rainfall' => 'required|numeric|min:0',
            'max_rainfall' => 'required|numeric|gt:min_rainfall',
            'min_humidity' => 'required|numeric|between:0,100',
            'max_humidity' => 'required|numeric|between:0,100|gt:min_humidity',
            'base_yield' => 'required|numeric|min:0.1',
        ]);

        $crop->update($validated);

        return redirect()->route('admin.crops')->with('success', "Crop '{$crop->name}' updated successfully.");
    }

    public function cropDestroy(Crop $crop)
    {
        $name = $crop->name;
        $crop->delete();

        return redirect()->route('admin.crops')->with('success', "Crop '{$name}' deleted.");
    }

    // User management
    public function users()
    {
        $users = User::withCount('predictions')->with('latestPrediction.crop')->latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->loadCount('predictions');
        $predictions = $user->predictions()->with('crop')->latest()->paginate(10, ['*'], 'predictions_page');

        $cropWiseStats = $user->predictions()->with('crop')->get()->groupBy('crop_id')->map(function ($group) {
            $first = $group->first();

            return [
                'crop_id' => $first->crop_id,
                'crop_name' => $first->crop->translated_crop_name ?? $first->crop->name ?? 'Unknown',
                'count' => $group->count(),
                'avg_yield' => round($group->avg('predicted_yield'), 2),
                'avg_suitability' => round($group->avg('suitability_score'), 1),
                'low_risk_count' => $group->where('risk_level', 'Low')->count(),
                'med_risk_count' => $group->where('risk_level', 'Medium')->count(),
                'high_risk_count' => $group->where('risk_level', 'High')->count(),
            ];
        })->values();

        return view('admin.show_user', compact('user', 'predictions', 'cropWiseStats'));
    }

    public function predictions()
    {
        $predictions = Prediction::with(['crop', 'user'])->latest()->paginate(15);

        return view('admin.predictions', compact('predictions'));
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
        }
        $user->delete();

        return redirect()->route('admin.users')->with('success', "User '{$user->name}' has been removed.");
    }

    public function destroyPrediction(Prediction $prediction)
    {
        $prediction->delete();
        $referer = request()->headers->get('referer');
        if ($referer && str_contains($referer, '/predictions/'.$prediction->id)) {
            return redirect()->route('predictions.index')->with('success', __('messages.prediction_delete_success'));
        }

        return back()->with('success', __('messages.prediction_delete_success'));
    }

    // Support ticket management
    public function ticketsIndex()
    {
        $tickets = SupportTicket::with('user')->latest()->paginate(15);
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    public function showTicket(SupportTicket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateTicketStatus(SupportTicket $ticket, Request $request)
    {
        $validated = $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update(['status' => $validated['status']]);

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function resolveTicket(SupportTicket $ticket, Request $request)
    {
        $validated = $request->validate(['admin_response' => 'required|string|min:10|max:5000']);

        $ticket->update(['status' => 'resolved', 'admin_response' => $validated['admin_response'], 'resolved_at' => now(), 'assigned_to' => auth()->id()]);

        try {
            Mail::mailer('support')
                ->to($ticket->user->email)
                ->send((new TicketResolvedMail($ticket))
                    ->from(env('SUPPORT_MAIL_FROM_ADDRESS', 'support.cropyield@gmail.com'), env('SUPPORT_MAIL_FROM_NAME', env('APP_NAME')))
                );
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket resolution email: '.$e->getMessage());
        }

        return back()->with('success', 'Ticket resolved and user notified via email.');
    }
}
