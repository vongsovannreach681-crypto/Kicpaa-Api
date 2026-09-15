<?php

namespace Tests\Feature;

use App\Models\addLink;
use App\Models\AddTheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_link_can_be_created_with_an_image(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/add-links', [
            'title' => 'KICPAA',
            'URL' => 'https://kicpaa.org.kh',
            'image' => $this->imageFile('logo.png'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'KICPAA')
            ->assertJsonPath('URL', 'https://kicpaa.org.kh');

        $link = addLink::first();
        Storage::disk('public')->assertExists($link->image);
    }

    public function test_a_link_can_be_created_without_an_image(): void
    {
        $response = $this->postJson('/api/add-links', [
            'title' => 'KICPAA',
            'URL' => 'https://kicpaa.org.kh',
        ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'KICPAA')
            ->assertJsonPath('URL', 'https://kicpaa.org.kh')
            ->assertJsonPath('image', null)
            ->assertJsonPath('image_url', null);

        $this->assertDatabaseHas('add_links', [
            'title' => 'KICPAA',
            'image' => null,
        ]);
    }

    public function test_an_image_can_be_replaced_and_the_link_deleted(): void
    {
        Storage::fake('public');
        $oldImage = $this->imageFile('old.png')->store('images', 'public');
        $link = addLink::create([
            'title' => 'Old title',
            'URL' => 'https://example.com',
            'image' => $oldImage,
        ]);

        $response = $this->putJson('/api/add-links/'.$link->id, [
            'title' => 'New title',
            'URL' => 'https://example.org',
            'image' => $this->imageFile('new.png'),
        ]);

        $response->assertOk()->assertJsonPath('title', 'New title');
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($link->fresh()->image);

        $this->deleteJson('/api/add-links/'.$link->id)->assertNoContent();
        $this->assertDatabaseMissing('add_links', ['id' => $link->id]);
    }

    public function test_an_image_can_be_added_when_the_link_has_no_existing_image(): void
    {
        Storage::fake('public');
        $link = addLink::create([
            'title' => 'KICPAA',
            'URL' => 'https://kicpaa.org.kh',
            'image' => null,
        ]);

        $this->postJson('/api/add-links/'.$link->id.'?_method=PUT', [
            'title' => $link->title,
            'URL' => $link->URL,
            'image' => $this->imageFile('new-logo.png'),
        ])->assertOk()->assertJsonPath('image', fn (string $path): bool => str_starts_with($path, 'images/'));

        Storage::disk('public')->assertExists($link->fresh()->image);
    }

    public function test_link_order_can_be_saved(): void
    {
        $first = addLink::create([
            'title' => 'First',
            'URL' => 'https://first.example.com',
            'position' => 0,
        ]);
        $second = addLink::create([
            'title' => 'Second',
            'URL' => 'https://second.example.com',
            'position' => 1,
        ]);

        $this->postJson('/api/add-links/reorder', [
            'ids' => [$second->id, $first->id],
        ])->assertOk();

        $this->assertDatabaseHas('add_links', [
            'id' => $second->id,
            'position' => 0,
        ]);
        $this->assertDatabaseHas('add_links', [
            'id' => $first->id,
            'position' => 1,
        ]);
    }

    public function test_a_link_view_is_counted(): void
    {
        $link = addLink::create([
            'title' => 'KICPAA',
            'URL' => 'https://kicpaa.org.kh',
        ]);

        $this->postJson('/api/add-links/'.$link->id.'/view')
            ->assertOk()
            ->assertJsonPath('view_count', 1);

        $this->assertDatabaseHas('add_links', [
            'id' => $link->id,
            'view_count' => 1,
        ]);
    }

    public function test_design_settings_can_be_loaded_and_saved(): void
    {
        $this->getJson('/api/design-settings')
            ->assertOk()
            ->assertJson([
                'theme' => 'midnight',
                'background' => 'deep-space',
            ]);

        $this->patchJson('/api/design-settings', [
            'theme' => 'neon',
            'background' => 'blueprint',
        ])->assertOk()->assertJson([
            'theme' => 'neon',
            'background' => 'blueprint',
        ]);

        $this->assertDatabaseHas('design_settings', [
            'id' => 1,
            'theme' => 'neon',
            'background' => 'blueprint',
        ]);
    }

    public function test_design_options_are_loaded_from_the_database(): void
    {
        $response = $this->getJson('/api/design-options')->assertOk();

        $response->assertJsonCount(15, 'themes')
            ->assertJsonCount(10, 'backgrounds')
            ->assertJsonPath('themes.0.id', 'midnight')
            ->assertJsonPath('backgrounds.0.id', 'deep-space');
    }

    public function test_a_theme_can_be_created_with_a_gif_and_deleted(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/addThemes', [
            'themeName' => 'Animated Theme',
            'themeImage' => UploadedFile::fake()->create('theme.gif', 20, 'image/gif'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('themeName', 'Animated Theme')
            ->assertJsonPath('themeImageUrl', fn (string $url): bool => str_contains($url, '/storage/themes/'));

        $theme = AddTheme::where('themeName', 'Animated Theme')->firstOrFail();
        Storage::disk('public')->assertExists($theme->themeImage);

        $this->getJson('/api/addThemes')->assertOk();
        $this->deleteJson('/api/addThemes/'.$theme->id)->assertNoContent();
        Storage::disk('public')->assertMissing($theme->themeImage);
    }

    public function test_a_theme_image_can_be_replaced(): void
    {
        Storage::fake('public');
        $oldImage = UploadedFile::fake()->create('old.gif', 20, 'image/gif');

        $this->postJson('/api/addThemes', [
            'themeName' => 'Old Theme',
            'themeImage' => $oldImage,
        ])->assertCreated();

        $theme = AddTheme::where('themeName', 'Old Theme')->firstOrFail();
        $oldPath = $theme->themeImage;
        Storage::disk('public')->assertExists($oldPath);

        $this->postJson('/api/addThemes/'.$theme->id.'?_method=PUT', [
            'themeName' => 'New Theme',
            'themeImage' => UploadedFile::fake()->create('new.png', 20, 'image/png'),
        ])->assertOk()->assertJsonPath('themeName', 'New Theme');

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($theme->fresh()->themeImage);
    }

    private function imageFile(string $name): UploadedFile
    {
        $content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');

        return UploadedFile::fake()->createWithContent($name, $content);
    }
}
