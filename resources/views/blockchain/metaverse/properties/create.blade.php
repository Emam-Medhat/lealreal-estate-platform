@extends('admin.layouts.admin')

@section('title', 'إضافة عقار جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('metaverse.properties.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        إضافة عقار جديد
                    </h1>
                </div>
            </div>
            <p class="text-gray-600 text-lg">
                أضف عقاراً جديداً إلى العالم الافتراضي
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <form action="{{ route('metaverse.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">المعلومات الأساسية</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-heading ml-2 text-blue-600"></i>
                                عنوان العقار
                            </label>
                            <input type="text" name="title" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="أدخل عنوان العقار">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-globe ml-2 text-blue-600"></i>
                                العالم الافتراضي
                            </label>
                            <select name="virtual_world_id" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white">
                                <option value="">اختر العالم الافتراضي</option>
                                @foreach($virtualWorlds as $world)
                                    <option value="{{ $world->id }}">{{ $world->name }}</option>
                                @endforeach
                            </select>
                            @error('virtual_world_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-tag ml-2 text-blue-600"></i>
                                نوع العقار
                            </label>
                            <select name="property_type" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white">
                                <option value="">اختر نوع العقار</option>
                                <option value="land">أرض</option>
                                <option value="building">مبنى</option>
                                <option value="apartment">شقة</option>
                                <option value="commercial">تجاري</option>
                                <option value="special">خاص</option>
                            </select>
                            @error('property_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-dollar-sign ml-2 text-blue-600"></i>
                                السعر
                            </label>
                            <input type="number" name="price" required step="0.01"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="0.00">
                            @error('price')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left ml-2 text-blue-600"></i>
                            وصف العقار
                        </label>
                        <textarea name="description" required rows="4"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white resize-none"
                                  placeholder="أدخل وصفاً مفصلاً للعقار"></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Location & Dimensions -->
                <div class="space-y-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-map-marked-alt text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">الموقع والأبعاد</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-map-pin ml-2 text-purple-600"></i>
                                إحداثيات الموقع
                            </label>
                            <input type="text" name="location_coordinates" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="x,y,z">
                            @error('location_coordinates')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-ruler-combined ml-2 text-purple-600"></i>
                                الأبعاد
                            </label>
                            <input type="text" name="dimensions" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="width x height x depth">
                            @error('dimensions')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="space-y-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-images text-indigo-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">الوسائط</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-camera ml-2 text-indigo-600"></i>
                            صور العقار
                        </label>
                        <input type="file" name="images[]" multiple accept="image/*"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-cube ml-2 text-indigo-600"></i>
                            نماذج ثلاثية الأبعاد (3D Models)
                        </label>
                        <input type="file" name="models[]" multiple accept=".glb,.gltf,.fbx,.obj"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-cog text-orange-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">خيارات إضافية</h2>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <input type="checkbox" name="create_nft" id="create_nft" value="1"
                               class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500 border-gray-300">
                        <label for="create_nft" class="font-medium text-gray-700 cursor-pointer select-none">
                            إنشاء NFT لهذا العقار تلقائياً
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-8">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold py-4 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save text-xl"></i>
                        <span class="text-lg">حفظ العقار</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
