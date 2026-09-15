<?php

namespace App\Http\Controllers;

use App\Models\addLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AddLinkController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(addLink::orderBy('position')->orderByDesc('created_at')->get()->map(
            fn (addLink $link): array => $this->linkData($link)
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'URL' => ['required', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        addLink::query()->increment('position');
        $validated['position'] = 0;
        $link = addLink::create($validated);

        return response()->json($this->linkData($link), 201);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:add_links,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['ids'] as $position => $id) {
                addLink::whereKey($id)->update(['position' => $position]);
            }
        });

        return response()->json(['message' => 'Links reordered.']);
    }

    public function recordView(addLink $addLink): JsonResponse
    {
        $addLink->increment('view_count');

        return response()->json($this->linkData($addLink->refresh()));
    }

    public function show(addLink $addLink): JsonResponse
    {
        return response()->json($this->linkData($addLink));
    }

    public function update(Request $request, addLink $addLink): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'URL' => ['sometimes', 'required', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($addLink->image) {
                Storage::disk('public')->delete($addLink->image);
            }

            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $addLink->update($validated);

        return response()->json($this->linkData($addLink->refresh()));
    }

    public function destroy(addLink $addLink): JsonResponse
    {
        // Storage::disk('public')->delete($addLink->image);
        $addLink->delete();

        return response()->json(null, 204);
    }

    private function linkData(addLink $link): array
    {
        return [
            'id' => $link->id,
            'title' => $link->title,
            'URL' => $link->URL,
            'image' => $link->image,
            'image_url' => $link->image ? $this->publicImageUrl($link->image) : null,
            'position' => $link->position,
            'view_count' => $link->view_count,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at,
        ];
    }

    private function publicImageUrl(string $path): string
    {
        $url = Storage::disk('public')->url($path);

        return parse_url($url, PHP_URL_PATH) ?: $url;
    }
}
