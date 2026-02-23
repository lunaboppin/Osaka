@props(['user', 'size' => 'sm'])

@php
    $roles = $user->relationLoaded('roles') ? $user->roles->sortByDesc('priority') : collect();
@endphp

@if($roles->isNotEmpty())
    <span class="inline-flex flex-wrap gap-1">
        @foreach($roles as $role)
            <span class="inline-flex items-center px-{{ $size === 'sm' ? '1.5' : '2.5' }} py-0.5 rounded-full text-{{ $size === 'sm' ? '[10px]' : 'xs' }} font-semibold text-white leading-none" style="background-color: {{ $role->color }}">
                {{ $role->display_name }}
            </span>
        @endforeach
    </span>
@endif
