@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-6">All Pins</h2>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b text-center">ID</th>
                    <th class="px-4 py-2 border-b text-center">Title</th>
                    <th class="px-4 py-2 border-b text-center">Status</th>
                    <th class="px-4 py-2 border-b text-center">User</th>
                    <th class="px-4 py-2 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pins as $pin)
                <tr>
                    <td class="px-4 py-2 border-b text-center">{{ $pin->id }}</td>
                    <td class="px-4 py-2 border-b text-center">{{ $pin->title }}</td>
                    <td class="px-4 py-2 border-b text-center">{{ $pin->status }}</td>
                    <td class="px-4 py-2 border-b text-center">{{ $pin->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border-b text-center">
                        <a href="{{ route('pins.edit', $pin) }}" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
