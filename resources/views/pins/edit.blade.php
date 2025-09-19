@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10 sm:px-6 lg:px-8">
    @if($pin->photo)
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('storage/' . $pin->photo) }}" alt="Pin Photo" class="rounded shadow max-h-60 max-w-full">
        </div>
    @endif
    <h2 class="text-2xl font-bold mb-2">Edit Pin</h2>
    <div class="mb-4 text-gray-700 text-sm">
        <span class="font-semibold">Originally added by:</span>
        {{ $pin->user->name ?? 'N/A' }}
    </div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('pins.update', $pin) }}" class="mb-4">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title', $pin->title) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="New" @if($pin->status=='New') selected @endif>New</option>
                <option value="Worn" @if($pin->status=='Worn') selected @endif>Worn</option>
                <option value="Needs replaced" @if($pin->status=='Needs replaced') selected @endif>Needs replaced</option>
            </select>
            @error('status')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
    <form method="POST" action="{{ route('pins.destroy', $pin) }}" onsubmit="return confirm('Delete this pin?');" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
    </form>
    <hr class="my-8">
    <h3 class="text-xl font-semibold mb-4">Add Update</h3>
    <form method="POST" action="{{ route('pins.updates.store', $pin) }}" enctype="multipart/form-data" class="mb-6">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="New">New</option>
                <option value="Worn">Worn</option>
                <option value="Needs replaced">Needs replaced</option>
            </select>
            @error('status')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Image (optional)</label>
            <input type="file" name="photo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('photo')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Update</button>
    </form>

    <h3 class="text-xl font-semibold mb-4">Updates</h3>
    @if($pin->updates && $pin->updates->count())
        <ul class="space-y-4">
            @foreach($pin->updates->sortByDesc('created_at') as $update)
                <li class="border rounded p-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold">Status: {{ $update->status }}</span>
                        <span class="text-sm text-gray-500">{{ $update->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="mb-2 text-gray-700 text-sm">
                        <span class="font-semibold">By:</span> {{ $update->user->name ?? 'N/A' }}
                    </div>
                    @if($update->photo)
                        <img src="{{ asset('storage/' . $update->photo) }}" alt="Update Photo" class="rounded shadow max-h-40 mb-2">
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-gray-500">No updates yet.</div>
    @endif

    <a href="{{ route('pins.index') }}" class="text-blue-600 hover:underline">Back to Pins</a>
</div>
@endsection
