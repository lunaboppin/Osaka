<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscordWebhook;
use App\Services\DiscordWebhookService;
use Illuminate\Http\Request;

class DiscordWebhookController extends Controller
{
    public function index()
    {
        $webhooks = DiscordWebhook::latest()->get();

        return view('admin.discord-webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        $availableEvents = DiscordWebhook::availableEvents();

        return view('admin.discord-webhooks.create', compact('availableEvents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|starts_with:https://discord.com/api/webhooks/,https://discordapp.com/api/webhooks/',
            'events' => 'nullable|array',
            'events.*' => 'string|in:' . implode(',', array_keys(DiscordWebhook::availableEvents())),
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['events'] = $validated['events'] ?? [];

        DiscordWebhook::create($validated);

        return redirect()->route('admin.discord-webhooks.index')
            ->with('success', "Webhook \"{$validated['name']}\" created!");
    }

    public function edit(DiscordWebhook $discordWebhook)
    {
        $availableEvents = DiscordWebhook::availableEvents();

        return view('admin.discord-webhooks.edit', compact('discordWebhook', 'availableEvents'));
    }

    public function update(Request $request, DiscordWebhook $discordWebhook)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|starts_with:https://discord.com/api/webhooks/,https://discordapp.com/api/webhooks/',
            'events' => 'nullable|array',
            'events.*' => 'string|in:' . implode(',', array_keys(DiscordWebhook::availableEvents())),
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['events'] = $validated['events'] ?? [];

        $discordWebhook->update($validated);

        return redirect()->route('admin.discord-webhooks.index')
            ->with('success', "Webhook \"{$discordWebhook->name}\" updated!");
    }

    public function destroy(DiscordWebhook $discordWebhook)
    {
        $name = $discordWebhook->name;
        $discordWebhook->delete();

        return redirect()->route('admin.discord-webhooks.index')
            ->with('success', "Webhook \"{$name}\" deleted.");
    }

    public function test(DiscordWebhook $discordWebhook, DiscordWebhookService $service)
    {
        $success = $service->test($discordWebhook);

        if ($success) {
            return back()->with('success', "Test message sent to \"{$discordWebhook->name}\" successfully!");
        }

        return back()->with('error', "Failed to send test message to \"{$discordWebhook->name}\". Check the URL.");
    }
}
