<?php

namespace App\Http\Controllers;

use App\Models\DesignSetting;
use App\Models\DesignOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignSettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json($this->settingData($this->setting()));
    }

    public function options(): JsonResponse
    {
        return response()->json([
            'themes' => $this->optionsByType('theme'),
            'backgrounds' => $this->optionsByType('background'),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['sometimes', 'required', 'string', 'max:80'],
            'background' => ['sometimes', 'required', 'string', 'max:80'],
            'textColor' => ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'boxColor' => ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $setting = $this->setting();
        $setting->update([
            ...array_intersect_key($validated, array_flip(['theme', 'background'])),
            ...array_filter([
                'text_color' => $validated['textColor'] ?? null,
                'box_color' => $validated['boxColor'] ?? null,
            ], fn ($value): bool => $value !== null),
        ]);

        return response()->json($this->settingData($setting->refresh()));
    }

    private function setting(): DesignSetting
    {
        return DesignSetting::firstOrCreate(
            ['id' => 1],
            [
                'theme' => 'midnight',
                'background' => 'deep-space',
                'text_color' => '#ffffff',
                'box_color' => '#64748b',
            ],
        );
    }

    private function settingData(DesignSetting $setting): array
    {
        return [
            'theme' => $setting->theme,
            'background' => $setting->background,
            'textColor' => $setting->text_color,
            'boxColor' => $setting->box_color,
        ];
    }

    private function optionsByType(string $type): array
    {
        return DesignOption::query()
            ->where('type', $type)
            ->orderBy('id')
            ->get(['slug', 'name', 'swatch'])
            ->map(fn (DesignOption $option): array => [
                'id' => $option->slug,
                'name' => $option->name,
                'swatch' => $option->swatch,
            ])
            ->all();
    }
}
