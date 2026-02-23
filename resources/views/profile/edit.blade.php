<x-app-layout>
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

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" x-data="{ avatarPreview: null, removeAvatar: false }">
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
                </form>
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
