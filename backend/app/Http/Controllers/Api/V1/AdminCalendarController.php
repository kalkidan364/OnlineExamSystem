<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCalendarController extends Controller
{
    // -------------------------------------------------------
    // EVENT CATEGORIES
    // -------------------------------------------------------

    public function index()
    {
        $categories = EventCategory::orderBy('type')->orderBy('name')->get();

        $stats = [
            'total'  => $categories->count(),
            'active' => $categories->where('status', 'active')->count(),
            'system' => $categories->where('type', 'system')->count(),
            'custom' => $categories->where('type', 'custom')->count(),
        ];

        return response()->json(['data' => $categories, 'stats' => $stats]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:event_categories,name',
            'description' => 'nullable|string|max:200',
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'type'        => ['required', Rule::in(['system', 'custom'])],
            'status'      => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $category = EventCategory::create([
            ...$validated,
            'status'     => $validated['status'] ?? 'active',
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['data' => $category], 201);
    }

    public function update(Request $request, string $id)
    {
        $category = EventCategory::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('event_categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string|max:200',
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'type'        => ['required', Rule::in(['system', 'custom'])],
            'status'      => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $category->update($validated);

        return response()->json(['data' => $category]);
    }

    public function destroy(string $id)
    {
        $category = EventCategory::findOrFail($id);

        if ($category->type === 'system') {
            return response()->json(['message' => 'System categories cannot be deleted.'], 403);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    // -------------------------------------------------------
    // ACADEMIC EVENTS
    // -------------------------------------------------------

    /**
     * List all events with stats and category info.
     */
    public function indexEvents(Request $request)
    {
        $events = AcademicEvent::with('category')
            ->orderBy('start_date')
            ->get()
            ->map(function ($e) {
                return [
                    'id'            => $e->id,
                    'title'         => $e->title,
                    'description'   => $e->description,
                    'category_id'   => $e->category_id,
                    'category_name' => $e->category?->name,
                    'category_color'=> $e->category?->color,
                    'academic_year' => $e->academic_year,
                    'semester'      => $e->semester,
                    'start_date'    => $e->start_date?->toDateString(),
                    'end_date'      => $e->end_date?->toDateString(),
                    'all_day'       => $e->all_day,
                    'start_time'    => $e->start_time,
                    'end_time'      => $e->end_time,
                    'status'        => $e->status,
                    'color'         => $e->color,
                    'is_recurring'  => $e->is_recurring,
                    'created_at'    => $e->created_at,
                ];
            });

        $now = now()->toDateString();

        $stats = [
            'total'    => $events->count(),
            'upcoming' => $events->where('status', 'upcoming')->count(),
            'ongoing'  => $events->where('status', 'ongoing')->count(),
            'holidays' => $events->filter(fn($e) => str_contains(strtolower($e['category_name'] ?? ''), 'holiday'))->count(),
            'exams'    => $events->filter(fn($e) => str_contains(strtolower($e['category_name'] ?? ''), 'exam'))->count(),
        ];

        return response()->json(['data' => $events, 'stats' => $stats]);
    }

    /**
     * Create a new academic event.
     */
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string|max:500',
            'category_id'   => 'nullable|exists:event_categories,id',
            'academic_year' => 'required|string|max:20',
            'semester'      => 'required|string|max:50',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'all_day'       => 'boolean',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i',
            'status'        => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
            'color'         => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_recurring'  => 'boolean',
        ]);

        $event = AcademicEvent::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        $event->load('category');

        return response()->json([
            'data' => [
                'id'            => $event->id,
                'title'         => $event->title,
                'description'   => $event->description,
                'category_id'   => $event->category_id,
                'category_name' => $event->category?->name,
                'category_color'=> $event->category?->color,
                'academic_year' => $event->academic_year,
                'semester'      => $event->semester,
                'start_date'    => $event->start_date?->toDateString(),
                'end_date'      => $event->end_date?->toDateString(),
                'all_day'       => $event->all_day,
                'start_time'    => $event->start_time,
                'end_time'      => $event->end_time,
                'status'        => $event->status,
                'color'         => $event->color,
                'is_recurring'  => $event->is_recurring,
            ]
        ], 201);
    }

    /**
     * Update an academic event.
     */
    public function updateEvent(Request $request, string $id)
    {
        $event = AcademicEvent::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string|max:500',
            'category_id'   => 'nullable|exists:event_categories,id',
            'academic_year' => 'required|string|max:20',
            'semester'      => 'required|string|max:50',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'all_day'       => 'boolean',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i',
            'status'        => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
            'color'         => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_recurring'  => 'boolean',
        ]);

        $event->update($validated);
        $event->load('category');

        return response()->json(['data' => array_merge($validated, ['id' => $event->id, 'category_name' => $event->category?->name])]);
    }

    /**
     * Delete an academic event.
     */
    public function destroyEvent(string $id)
    {
        $event = AcademicEvent::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }
}
