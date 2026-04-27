@extends('layouts.dashboard')

@section('title', 'Meseriași - Administrator')
@section('page-title', 'Gestionare Meseriași')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('header-actions')
<form action="{{ route('admin.craftsmen') }}" method="GET" class="flex items-center space-x-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută..." 
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
    
    <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate categoriile</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toate statusurile</option>
        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Activi</option>
        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactivi</option>
    </select>
    
    <select name="is_featured" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
        <option value="">Toți</option>
        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Recomandați</option>
        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Nerecomandați</option>
    </select>

    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
        Filtrează
    </button>
</form>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meseriaș</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locație</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statusuri</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($craftsmen as $craftsman)
                <tr class="{{ $craftsman->is_featured ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-secondary-200 rounded-full flex items-center justify-center text-primary-600 font-bold relative">
                                {{ strtoupper(substr($craftsman->name, 0, 1)) }}
                                @if($craftsman->is_verified)
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    {{ $craftsman->name }}
                                    @if($craftsman->is_featured)
                                        <span class="px-1.5 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">⭐ Top</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">{{ $craftsman->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $craftsman->category->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $craftsman->location->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($craftsman->reviews_count > 0)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="ml-1 text-sm text-gray-900">{{ number_format($craftsman->reviews_avg_rating, 1) }}</span>
                                <span class="ml-1 text-xs text-gray-500">({{ $craftsman->reviews_count }})</span>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">Fără recenzii</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $craftsman->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $craftsman->is_active ? 'Activ' : 'Inactiv' }}
                            </span>
                            @if($craftsman->is_verified)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                    Verificat
                                </span>
                            @endif
                            @if($craftsman->is_featured)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Recomandat
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <!-- View Profile -->
                            <a href="{{ route('craftsman.show', $craftsman->slug) }}" target="_blank" 
                               class="text-gray-500 hover:text-gray-700" title="Vezi profil">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            
                            <!-- Edit -->
                            <a href="{{ route('admin.craftsmen.edit', $craftsman->id) }}" 
                               class="text-blue-600 hover:text-blue-800" title="Editează">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            
                            <!-- Toggle Featured -->
                            <form action="{{ route('admin.craftsmen.toggle-featured', $craftsman->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="{{ $craftsman->is_featured ? 'text-yellow-500 hover:text-yellow-700' : 'text-gray-400 hover:text-yellow-500' }}"
                                        title="{{ $craftsman->is_featured ? 'Elimină din recomandări' : 'Adaugă la recomandări' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </form>
                            
                            <!-- Toggle Verified -->
                            <form action="{{ route('admin.craftsmen.toggle-verified', $craftsman->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="{{ $craftsman->is_verified ? 'text-blue-500 hover:text-blue-700' : 'text-gray-400 hover:text-blue-500' }}"
                                        title="{{ $craftsman->is_verified ? 'Elimină verificarea' : 'Marchează ca verificat' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                            
                            <!-- Toggle Active -->
                            <form action="{{ route('admin.craftsmen.toggle-status', $craftsman->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="{{ $craftsman->is_active ? 'text-green-500 hover:text-red-500' : 'text-red-500 hover:text-green-500' }}"
                                        title="{{ $craftsman->is_active ? 'Dezactivează' : 'Activează' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        @if($craftsman->is_active)
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @else
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Nu s-au găsit meseriași
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Legend -->
<div class="mt-4 flex items-center gap-6 text-sm text-gray-500">
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        = Recomandat/Top
    </span>
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        = Verificat
    </span>
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        = Activ/Inactiv
    </span>
</div>

<div class="mt-6">
    {{ $craftsmen->links() }}
</div>
@endsection
