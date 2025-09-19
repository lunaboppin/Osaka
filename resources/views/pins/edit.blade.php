@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-6">Edit Pin</h2>
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
            <form method="POST" action="{{ route('pins.destroy', $pin) }}" onsubmit="return confirm('Delete this pin?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
            </form>
        </div>
    </form>
    <a href="{{ route('pins.index') }}" class="text-blue-600 hover:underline">Back to Pins</a>
</div>
@endsection
