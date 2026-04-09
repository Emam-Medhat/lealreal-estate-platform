@extends('layouts.app')

@section('title', 'Add Property Photos')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add Property Photos</h1>
                <p class="text-gray-600 mt-2">{{ $property->title }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('agent.properties.show', $property) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Property
                </a>
            </div>
        </div>
    </div>

    <!-- Photo Upload Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('agent.properties.upload-photos', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Current Photos -->
            @if($property->media->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Current Photos</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($property->media as $media)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->file_name }}" class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                                    <button type="button" onclick="deletePhoto({{ $media->id }})" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @if($media->is_featured)
                                    <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload New Photos -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition">
                    <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="hidden">
                    <label for="photos" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Click to upload photos or drag and drop</p>
                        <p class="text-sm text-gray-500 mt-1">PNG, JPG, GIF up to 10MB each</p>
                    </label>
                </div>
                <div id="photo-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4"></div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Photos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Photo preview
document.getElementById('photos').addEventListener('change', function(e) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-32 object-cover rounded-lg">
                    <span class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">${file.name}</span>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Delete photo
function deletePhoto(photoId) {
    if (confirm('Are you sure you want to delete this photo?')) {
        fetch(`/agent/properties/photos/${photoId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete photo');
        });
    }
}
</script>
@endsection
