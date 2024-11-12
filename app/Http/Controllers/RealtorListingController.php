<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Inertia\Inertia;
use Inertia\Response;

class RealtorListingController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(Listing::class, 'listing');
    // }

    public function index(Request $request)
    {
        FacadesGate::authorize(
            'viewAny',
            Listing::class
        );
        $filters = [
            'deleted' => $request->boolean('deleted'),
            ...$request->only(['by', 'order'])
        ];

        return Inertia::render(
            'Realtor/Index',
            [
                'filters' => $filters,
                'listings' => Auth::user()->listings()
                    ->filter($filters)
                    ->withCount('images')
                    ->withCount('offers')
                    ->paginate(5)
                    ->withQueryString()
            ]
        );
    }

    public function show(Listing $listing): Response
    {
        FacadesGate::authorize(
            'view',
            $listing
        );
        return Inertia::render(
            'Realtor/Show',
            [
                'listing' => $listing->load(
                    'offers',
                    'offers.bidder'
                )
            ]
        );
    }

    public function create(): Response
    {
        FacadesGate::authorize(
            'create',
            Listing::class
        );
        return Inertia::render('Realtor/Create');
    }

    public function store(Request $request)
    {
        FacadesGate::authorize(
            'create',
            Listing::class
        );
        $request->user()->listings()->create(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths' => 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:15|max:1500',
                'city' => 'required',
                'code' => 'required',
                'street' => 'required',
                'street_nr' => 'required|min:1|max:1000',
                'price' => 'required|integer|min:1|max:20000000',
            ])
        );

        return redirect()->route('realtor.listing.index')
            ->with('success', 'Listing was created!');
    }

    public function edit(Listing $listing)
    {
        FacadesGate::authorize(
            'update',
            $listing
        );
        return Inertia::render(
            'Realtor/Edit',
            [
                'listing' => $listing
            ]
        );
    }

    public function update(Request $request, Listing $listing)
    {
        FacadesGate::authorize(
            'update',
            $listing
        );
        $listing->update(
            $request->validate([
                'beds' => 'required|integer|min:0|max:20',
                'baths' => 'required|integer|min:0|max:20',
                'area' => 'required|integer|min:15|max:1500',
                'city' => 'required',
                'code' => 'required',
                'street' => 'required',
                'street_nr' => 'required|min:1|max:1000',
                'price' => 'required|integer|min:1|max:20000000',
            ])
        );

        return redirect()->route('realtor.listing.index')
            ->with('success', 'Listing was changed!');
    }

    public function destroy(Listing $listing)
    {
        FacadesGate::authorize(
            'delete',
            $listing
        );
        $listing->deleteOrFail();

        return redirect()->back()
            ->with('success', 'Listing was deleted!');
    }

    public function restore(Listing $listing)
    {
        FacadesGate::authorize(
            'restore',
            $listing
        );
        $listing->restore();

        return redirect()->back()->with('success', 'Listing was restored!');
    }
}
