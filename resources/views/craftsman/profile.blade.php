@extends('layouts.craftsman')

@section('title', 'Profilul Meu')
@section('page-title', 'Profilul Meu')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('craftsman.profile.update') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nume Complet</label>
                <input type="text" name="name" value="{{ old('name', $craftsman->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $craftsman->phone) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
                @error('phone')
                    <p class="mt-1 text-sm text-error-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ani Experiență</label>
                <input type="number" name="experience_years" value="{{ old('experience_years', $craftsman->experience_years) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Specializare</label>
                <input type="text" name="specialization" value="{{ old('specialization', $craftsman->specialization) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rază Servicii (km)</label>
                <input type="number" name="service_radius_km" value="{{ old('service_radius_km', $craftsman->service_radius_km) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descriere</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent">{{ old('description', $craftsman->description) }}</textarea>
            </div>

            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" name="available_weekends" value="1" {{ $craftsman->available_weekends ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Disponibil weekend</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="emergency_services" value="1" {{ $craftsman->emergency_services ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Ofer servicii de urgență</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="has_insurance" value="1" {{ $craftsman->has_insurance ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Am asigurare profesională</label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
                Salvează Modificările
            </button>
        </div>
    </form>
</div>
@endsection
