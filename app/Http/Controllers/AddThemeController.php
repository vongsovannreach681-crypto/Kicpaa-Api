<?php

namespace App\Http\Controllers;

use App\Models\AddTheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddThemeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            AddTheme::query()->latest()->get()->map(
                fn (AddTheme $theme): array => $this->themeData($theme)
            )
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'themeName' => ['required', 'string', 'max:80'],
            'themeImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $validated['themeImage'] = $request->file('themeImage')->store('themes', 'public');
        $theme = AddTheme::create($validated);

        return response()->json($this->themeData($theme), 201);
    }

    public function show(AddTheme $addTheme): JsonResponse
    {
        return response()->json($this->themeData($addTheme));
    }

    public function update(Request $request, AddTheme $addTheme): JsonResponse
    {
        $validated = $request->validate([
            'themeName' => ['sometimes', 'required', 'string', 'max:80'],
            'themeImage' => ['sometimes', 'required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        if ($request->hasFile('themeImage')) {
            Storage::disk('public')->delete($addTheme->themeImage);
            $validated['themeImage'] = $request->file('themeImage')->store('themes', 'public');
        }

        $addTheme->update($validated);

        return response()->json($this->themeData($addTheme->refresh()));
    }

    public function destroy(AddTheme $addTheme): JsonResponse
    {
        Storage::disk('public')->delete($addTheme->themeImage);
        $addTheme->delete();

        return response()->json(null, 204);
    }

    private function themeData(AddTheme $theme): array
    {
        return [
            'id' => $theme->id,
            'themeName' => $theme->themeName,
            'themeImage' => $theme->themeImage,
            'themeImageUrl' => $theme->themeImage
                ? Storage::disk('public')->url($theme->themeImage)
                : null,
            'created_at' => $theme->created_at,
            'updated_at' => $theme->updated_at,
        ];
    }
}
