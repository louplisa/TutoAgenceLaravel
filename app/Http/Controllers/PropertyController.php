<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyContactRequest;
use App\Http\Requests\SearchPropertiesRequest;
use App\Mail\PropertyContactMail;
use App\Models\Property;
use App\Notifications\ContactRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(SearchPropertiesRequest $request)
    {
        $query = Property::query()->with('pictures')->orderBy('created_at', 'desc');
        if ($price = $request->validated('price')) {
            $query = $query->where('price', '<=', $price);
        }
        if ($surface = $request->validated('surface')) {
            $query = $query->where('surface', '>=', $surface);
        }
        if ($rooms = $request->validated('rooms')) {
            $query = $query->where('rooms', '>=', $rooms);
        }
        if ($title = $request->validated('title')) {
            $query = $query->where('title', 'like', "%{$title}%");
        }
        return view('property.index', [
            'properties' => $query->paginate(16),
            'input' => $request->validated(),
        ]);
    }

    public function show(string $slug, Property $property): RedirectResponse|View
    {
        // DemoJob::dispatch($property)->delay(now()->addSecond(10));
        $expectedSlug = $property->getSlug();
        if ($slug !== $expectedSlug) {
            return to_route('property.show', [
                'slug' => $expectedSlug,
                'property' => $property,
            ]);
        }
        return view('property.show', [
            'property' => $property,
        ]);
    }

    public function contact(Property $property, PropertyContactRequest $request): RedirectResponse
    {
//        event(new ContactRequestEvent($property, $request->validated()));
//        $user = User::first();
        Notification::route('mail', 'john@admin.fr')->notify(new ContactRequestNotification($property, $request->validated()));
        Mail::send(new PropertyContactMail($property, $request->validated()));
        return back()->with('success', 'Thanks for contacting us!');
    }
}
