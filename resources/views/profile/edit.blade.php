<x-app-layout>
    <x-slot name="pageTitle">Edit Profile</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- User Info Card --}}
        <div class="card">
            <div class="card-body">
                @if(session('status') === 'profile-updated')
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center" x-data="{ show: true }" x-show="show" x-transition>
                        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Profile updated!
                        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                      x-data="{
                          avatarPreview: null,
                          removeAvatar: false,
                          bannerPreview: null,
                          removeBanner: false,
                          accentColor: '{{ old('accent_color', $user->accent_color ?? '') }}',
                          clearAccent: false,
                      }">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col sm:flex-row items-start gap-6">
                        {{-- Avatar with upload --}}
                        <div class="shrink-0">
                            <div class="relative group">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-24 h-24 rounded-full object-cover border-4 border-osaka-gold/30 shadow">
                                </template>
                                <template x-if="!avatarPreview && !removeAvatar">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-osaka-gold/30 shadow">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-osaka-gold flex items-center justify-center text-3xl font-bold text-osaka-charcoal border-4 border-osaka-gold/30 shadow">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </template>
                                <template x-if="!avatarPreview && removeAvatar">
                                    <div class="w-24 h-24 rounded-full bg-osaka-gold flex items-center justify-center text-3xl font-bold text-osaka-charcoal border-4 border-osaka-gold/30 shadow">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </template>

                                {{-- Upload overlay --}}
                                <label class="absolute inset-0 rounded-full bg-black/0 group-hover:bg-black/40 flex items-center justify-center cursor-pointer transition-all">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <input type="file" name="avatar" accept="image/*" class="hidden"
                                           @change="removeAvatar = false; const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => avatarPreview = e.target.result; r.readAsDataURL(f); }">
                                </label>
                            </div>
                            {{-- Remove avatar button --}}
                            @if($user->avatar)
                                <button type="button"
                                        x-show="!removeAvatar"
                                        @click="removeAvatar = true; avatarPreview = null"
                                        class="mt-2 text-xs text-gray-400 hover:text-red-500 transition-colors w-full text-center">
                                    Remove photo
                                </button>
                            @endif
                            <input type="hidden" name="remove_avatar" :value="removeAvatar ? 1 : 0">
                            @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Name + info --}}
                        <div class="flex-1 w-full space-y-4">
                            <div>
                                <label for="name" class="form-label">Display Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-input-osaka" required>
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="bio" class="form-label">Bio <span class="font-normal text-gray-400">(optional)</span></label>
                                <textarea name="bio" id="bio" rows="3" class="form-input-osaka" maxlength="500" placeholder="Tell people a bit about yourself...">{{ old('bio', $user->bio) }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Up to 500 characters</p>
                                @error('bio') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label text-gray-400">Email</label>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Managed by your authentication provider</p>
                            </div>
                            @if(isset($stickerTypes) && $stickerTypes->count() > 0)
                                <div>
                                    <label for="default_sticker_type_id" class="form-label">Default Sticker Type</label>
                                    <select name="default_sticker_type_id" id="default_sticker_type_id" class="form-input-osaka">
                                        <option value="">All Types (no default)</option>
                                        @foreach($stickerTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('default_sticker_type_id', $user->default_sticker_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Automatically selected when you log in or start a new session</p>
                                    @error('default_sticker_type_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif
                            <p class="text-xs text-gray-400">
                                Member since {{ $user->created_at?->format('j F Y') }}
                            </p>
                            {{-- Role badges --}}
                            @if($user->roles->count() > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($user->roles->sortByDesc('priority') as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $role->color }}">
                                            {{ $role->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="pt-2">
                                <button type="submit" class="btn-primary btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ Profile Customisation Section ═══ --}}
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-osaka-charcoal mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            Profile Customisation
                        </h3>

                        <div class="space-y-6">
                            {{-- Profile Banner --}}
                            <div>
                                <label class="form-label">Profile Banner</label>
                                <p class="text-xs text-gray-400 mb-2">Displayed behind your avatar on your public profile. Recommended: 1200×300px.</p>

                                {{-- Banner preview --}}
                                <div class="relative rounded-lg overflow-hidden mb-3 h-32 {{ !$user->banner_path ? 'bg-gradient-to-r from-osaka-charcoal to-osaka-charcoal-light' : '' }}">
                                    <template x-if="bannerPreview">
                                        <img :src="bannerPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!bannerPreview && !removeBanner">
                                        @if($user->banner_url)
                                            <img src="{{ $user->banner_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </template>
                                    <template x-if="!bannerPreview && removeBanner">
                                        <div class="w-full h-full bg-gradient-to-r from-osaka-charcoal to-osaka-charcoal-light flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="btn-secondary btn-sm cursor-pointer">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        Upload Banner
                                        <input type="file" name="banner" accept="image/*" class="hidden"
                                               @change="removeBanner = false; const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => bannerPreview = e.target.result; r.readAsDataURL(f); }">
                                    </label>
                                    @if($user->banner_path)
                                        <button type="button" x-show="!removeBanner"
                                                @click="removeBanner = true; bannerPreview = null"
                                                class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                                            Remove banner
                                        </button>
                                    @endif
                                </div>
                                <input type="hidden" name="remove_banner" :value="removeBanner ? 1 : 0">
                                @error('banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Accent Colour --}}
                            <div>
                                <label class="form-label">Accent Colour</label>
                                <p class="text-xs text-gray-400 mb-2">Customise the highlight colour on your profile card. Leave blank to use the theme default.</p>
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <input type="color" name="accent_color"
                                               :value="accentColor || '{{ $user->effective_accent_color }}'"
                                               @input="accentColor = $event.target.value; clearAccent = false"
                                               class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                                    </div>
                                    <span class="text-sm text-gray-500 font-mono" x-text="accentColor || 'Theme default'"></span>
                                    @if($user->accent_color)
                                        <button type="button"
                                                @click="clearAccent = true; accentColor = ''"
                                                class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                                            Reset to theme default
                                        </button>
                                    @endif
                                </div>
                                <input type="hidden" name="clear_accent_color" :value="clearAccent ? 1 : 0">
                                @error('accent_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Profile Theme --}}
                            <div>
                                <label class="form-label">Profile Theme</label>
                                <p class="text-xs text-gray-400 mb-3">Choose a visual theme for your profile card. Higher-level themes unlock as you earn XP.</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($themes as $key => $theme)
                                        @php
                                            $locked = ($theme['min_level'] ?? 1) > $user->level;
                                            $selected = old('profile_theme', $user->profile_theme ?? 'default') === $key;
                                        @endphp
                                        <label class="relative cursor-pointer {{ $locked ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            <input type="radio" name="profile_theme" value="{{ $key }}"
                                                   {{ $selected ? 'checked' : '' }}
                                                   {{ $locked ? 'disabled' : '' }}
                                                   class="sr-only peer">
                                            <div class="rounded-lg overflow-hidden border-2 transition-all
                                                        {{ $selected ? 'border-osaka-gold ring-2 ring-osaka-gold/30' : 'border-gray-200 hover:border-gray-300' }}
                                                        peer-checked:border-osaka-gold peer-checked:ring-2 peer-checked:ring-osaka-gold/30">
                                                {{-- Theme preview strip --}}
                                                <div class="h-10 {{ $theme['banner_bg'] }}"></div>
                                                <div class="px-3 py-2 bg-white">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-osaka-charcoal">{{ $theme['label'] }}</span>
                                                        @if($locked)
                                                            <span class="text-xs text-gray-400">Lvl {{ $theme['min_level'] }}</span>
                                                        @else
                                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $theme['accent'] }}"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($locked)
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                                </div>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                                @error('profile_theme') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Avatar Frame --}}
                            <div>
                                <label class="form-label">Avatar Frame</label>
                                <p class="text-xs text-gray-400 mb-3">Decorative border around your avatar. Unlock frames by levelling up.</p>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($avatarFrames as $key => $frame)
                                        @php
                                            $locked = ($frame['min_level'] ?? 1) > $user->level;
                                            $currentFrame = old('avatar_frame', $user->avatar_frame);
                                            $isSelected = ($currentFrame === $key) || ($key === 'none' && !$currentFrame);
                                        @endphp
                                        <label class="flex flex-col items-center gap-1.5 cursor-pointer {{ $locked ? 'opacity-40 cursor-not-allowed' : '' }}">
                                            <input type="radio" name="avatar_frame" value="{{ $key }}"
                                                   {{ $isSelected ? 'checked' : '' }}
                                                   {{ $locked ? 'disabled' : '' }}
                                                   class="sr-only peer">
                                            <div class="relative p-1 rounded-full transition-all
                                                        peer-checked:ring-2 peer-checked:ring-osaka-gold peer-checked:ring-offset-2">
                                                <div class="w-14 h-14 rounded-full bg-osaka-cream flex items-center justify-center text-lg font-bold text-osaka-charcoal {{ $frame['border_class'] }} {{ $frame['ring_class'] ?? '' }}">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                @if($locked)
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-xs {{ $locked ? 'text-gray-400' : 'text-gray-600' }}">{{ $frame['label'] }}</span>
                                            @if($locked)
                                                <span class="text-xs text-gray-400">Lvl {{ $frame['min_level'] }}</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                                @error('avatar_frame') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Badge Display --}}
                            <div>
                                <label class="form-label">Badge Display</label>
                                <p class="text-xs text-gray-400 mb-3">Choose up to {{ config('osaka.profile.max_displayed_badges', 5) }} badges to show on your public profile. Badges are earned from your activity.</p>

                                @php $available = $user->available_badges; $displayed = $user->displayed_badges ?? []; @endphp

                                @if(count($available) > 0)
                                    <div class="space-y-2">
                                        @foreach($available as $key => $badge)
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                                <input type="checkbox" name="displayed_badges[]" value="{{ $key }}"
                                                       {{ in_array($key, old('displayed_badges', $displayed)) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-osaka-red focus:ring-osaka-red">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                                      style="background-color: {{ $badge['color'] }}">
                                                    @if($badge['icon'] === 'star')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @elseif($badge['icon'] === 'map-pin')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                                    @elseif($badge['icon'] === 'bolt')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                                    @elseif($badge['icon'] === 'clock')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                    @elseif($badge['icon'] === 'user-check')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"/></svg>
                                                    @endif
                                                    {{ $badge['label'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-400">No badges available yet. Keep using Osaka to earn them!</p>
                                @endif
                                @error('displayed_badges') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="btn-primary btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Save All Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- XP & Level Card --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-bold text-osaka-charcoal mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Your Level
                </h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-osaka-gold to-osaka-gold-light flex items-center justify-center text-xl font-bold text-osaka-charcoal shadow shrink-0">
                            {{ $user->level }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <x-level-badge :user="$user" size="sm" />
                                <span class="text-sm text-gray-500">{{ number_format($user->total_xp) }} XP</span>
                            </div>
                            <x-xp-progress-bar :user="$user" :showLabel="true" class="mt-2" />
                        </div>
                    </div>
                    <a href="{{ route('users.activity', $user) }}" class="btn-secondary text-sm shrink-0">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        View Activity
                    </a>
                </div>
            </div>
        </div>

        {{-- Pin Statistics --}}
        <div>
            <h3 class="text-lg font-bold text-osaka-charcoal mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Your Pin Statistics
            </h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card
                    :value="$pinStats['total']"
                    label="Total Pins"
                    color="osaka-charcoal"
                    icon='<svg class="w-6 h-6 text-osaka-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['new']"
                    label="New"
                    color="emerald-600"
                    icon='<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['worn']"
                    label="Worn"
                    color="amber-500"
                    icon='<svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['needs_replaced']"
                    label="Needs Replaced"
                    color="red-500"
                    icon='<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                />
            </div>
        </div>

        {{-- Recent Pins --}}
        @if($recentPins->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-osaka-charcoal flex items-center">
                        <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your Recent Pins
                    </h3>
                    <a href="{{ route('pins.index', ['mine' => 1]) }}" class="text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                        View all &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($recentPins as $pin)
                        <x-pin-card :pin="$pin" :showActions="true" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Delete Account Section --}}
        <div class="card border-red-200">
            <div class="p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
