@extends('layouts.dashboard')

@section('title', 'Editare Meseriaș - Administrator')
@section('page-title', 'Editare Meseriaș: ' . $craftsman->name)

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<div class="flex items-center gap-4">
    <a href="{{ route('craftsman.show', $craftsman->slug) }}" target="_blank" 
       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Vezi profil public
    </a>
    <a href="{{ route('admin.craftsmen') }}" class="text-gray-600 hover:text-gray-800">
        ← Înapoi la listă
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <form action="{{ route('admin.craftsmen.update', $craftsman->id) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')
            
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informații de bază</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nume</label>
                    <input type="text" name="name" value="{{ old('name', $craftsman->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $craftsman->email) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $craftsman->phone) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specializare</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $craftsman->specialization) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                        <option value="">Selectează categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $craftsman->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Locație</label>
                    <select name="location_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                        <option value="">Selectează locația</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $craftsman->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Experiență (ani)</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', $craftsman->experience_years) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descriere</label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">{{ old('description', $craftsman->description) }}</textarea>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Coordonate GPS (pentru căutare prin proximitate)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitudine</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $craftsman->latitude) }}" 
                           placeholder="ex: 44.4268"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitudine</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $craftsman->longitude) }}" 
                           placeholder="ex: 26.1025"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                </div>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Statusuri și Promovare</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $craftsman->is_active ? 'border-green-300 bg-green-50' : '' }}">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $craftsman->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Activ</span>
                </label>
                
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $craftsman->is_featured ? 'border-yellow-300 bg-yellow-50' : '' }}">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $craftsman->is_featured) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">⭐ Recomandat/Top</span>
                </label>
                
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $craftsman->is_verified ? 'border-blue-300 bg-blue-50' : '' }}">
                    <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $craftsman->is_verified) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">✓ Verificat</span>
                </label>
                
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="available_weekends" value="1" {{ old('available_weekends', $craftsman->available_weekends) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Weekend</span>
                </label>
                
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="emergency_services" value="1" {{ old('emergency_services', $craftsman->emergency_services) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Urgențe</span>
                </label>
                
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="has_insurance" value="1" {{ old('has_insurance', $craftsman->has_insurance) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Asigurat</span>
                </label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Salvează modificările
                </button>
            </div>
        </form>
    </div>
    
    <!-- Sidebar Stats -->
    <div class="space-y-6">
        <!-- Stats Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Statistici</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Servicii</span>
                    <span class="font-semibold text-gray-900">{{ $craftsman->services_count ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Recenzii</span>
                    <span class="font-semibold text-gray-900">{{ $craftsman->reviews_count ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Poze galerie</span>
                    <span class="font-semibold text-gray-900">{{ $craftsman->gallery_count ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Rating mediu</span>
                    <span class="font-semibold text-gray-900 flex items-center">
                        @if($craftsman->reviews_count > 0)
                            <svg class="w-4 h-4 text-accent-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ number_format($craftsman->reviews->avg('rating'), 1) }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Înregistrat</span>
                    <span class="font-semibold text-gray-900">{{ $craftsman->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Acțiuni rapide</h3>
            
            <div class="space-y-3">
                <form action="{{ route('admin.craftsmen.toggle-featured', $craftsman->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded-lg border {{ $craftsman->is_featured ? 'bg-yellow-100 border-yellow-300 text-yellow-800' : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-yellow-50' }} transition">
                        @if($craftsman->is_featured)
                            ⭐ Elimină din Recomandări
                        @else
                            ☆ Adaugă la Recomandări
                        @endif
                    </button>
                </form>
                
                <form action="{{ route('admin.craftsmen.toggle-verified', $craftsman->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded-lg border {{ $craftsman->is_verified ? 'bg-blue-100 border-blue-300 text-blue-800' : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-blue-50' }} transition">
                        @if($craftsman->is_verified)
                            ✓ Elimină Verificarea
                        @else
                            ○ Marchează Verificat
                        @endif
                    </button>
                </form>
                
                <form action="{{ route('admin.craftsmen.toggle-status', $craftsman->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded-lg border {{ $craftsman->is_active ? 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' : 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100' }} transition">
                        @if($craftsman->is_active)
                            Dezactivează Contul
                        @else
                            Activează Contul
                        @endif
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Recent Reviews -->
        @if($craftsman->reviews->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recenzii recente</h3>
            
            <div class="space-y-3">
                @foreach($craftsman->reviews->take(3) as $review)
                    <div class="border-b pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-accent-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-2 text-xs text-gray-500">{{ $review->created_at->format('d.m.Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
