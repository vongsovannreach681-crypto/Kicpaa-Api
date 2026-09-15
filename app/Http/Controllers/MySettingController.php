<?php

namespace App\Http\Controllers;

use App\Models\MySetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MySettingController extends Controller
{
    private const IMAGE_DIR = 'my-settings';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(MySetting::query()->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store(self::IMAGE_DIR, 'public');
        }

        $mySetting = MySetting::create($validated);

        return response()->json($mySetting, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(MySetting $mySetting)
    {
        return response()->json($mySetting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MySetting $mySetting)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'short_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($mySetting->image) {
                Storage::disk('public')->delete($mySetting->image);
            }
            $validated['image'] = $request->file('image')->store(self::IMAGE_DIR, 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($mySetting->image) {
                Storage::disk('public')->delete($mySetting->image);
            }
            $validated['image'] = null;
        }

        unset($validated['remove_image']);

        $mySetting->update($validated);

        return response()->json($mySetting->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MySetting $mySetting)
    {
        if ($mySetting->image) {
            Storage::disk('public')->delete($mySetting->image);
        }

        $mySetting->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
