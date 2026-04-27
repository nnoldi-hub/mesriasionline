@extends('layouts.client')

@section('title', 'Editează Adresa')
@section('page-title', 'Editează Adresa: ' . $address->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('client.addresses.update', $address) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Denumire adresă -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Denumire Adresă <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $address->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('name') border-red-400 @enderror"
                    placeholder="ex: Acasă, Birou, Părinți">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Strada și număr -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="street" class="block text-sm font-medium text-gray-700 mb-1">
                        Strada <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="street" name="street" value="{{ old('street', $address->street) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('street') border-red-400 @enderror"
                        placeholder="Strada Exemplu">
                    @error('street')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 mb-1">
                        Număr
                    </label>
                    <input type="text" id="number" name="number" value="{{ old('number', $address->number) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="123">
                </div>
            </div>

            <!-- Bloc, scara, etaj, apartament -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="building" class="block text-sm font-medium text-gray-700 mb-1">
                        Bloc
                    </label>
                    <input type="text" id="building" name="building" value="{{ old('building', $address->building) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="A1">
                </div>
                <div>
                    <label for="entrance" class="block text-sm font-medium text-gray-700 mb-1">
                        Scara
                    </label>
                    <input type="text" id="entrance" name="entrance" value="{{ old('entrance', $address->entrance) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="B">
                </div>
                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700 mb-1">
                        Etaj
                    </label>
                    <input type="text" id="floor" name="floor" value="{{ old('floor', $address->floor) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="3">
                </div>
                <div>
                    <label for="apartment" class="block text-sm font-medium text-gray-700 mb-1">
                        Apartament
                    </label>
                    <input type="text" id="apartment" name="apartment" value="{{ old('apartment', $address->apartment) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="42">
                </div>
            </div>

            <!-- Oraș și Județ -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        Oraș <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city" value="{{ old('city', $address->city) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('city') border-red-400 @enderror"
                        placeholder="București">
                    @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="county" class="block text-sm font-medium text-gray-700 mb-1">
                        Județ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="county" name="county" value="{{ old('county', $address->county) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600 @error('county') border-red-400 @enderror"
                        placeholder="București">
                    @error('county')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Cod poștal și zonă -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Cod Poștal
                    </label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                        placeholder="010101">
                </div>
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Zonă (pentru căutare meșteri)
                    </label>
                    <select id="location_id" name="location_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600">
                        <option value="">-- Selectează zona --</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id', $address->location_id) == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Note -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Indicații suplimentare
                </label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-600 focus:border-primary-600"
                    placeholder="ex: Interfon 42, codul de la poartă: 1234...">{{ old('notes', $address->notes) }}</textarea>
            </div>

            <!-- Adresă implicită -->
            <div class="flex items-center">
                <input type="checkbox" id="is_default" name="is_default" value="1" 
                    {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_default" class="ml-2 text-sm text-gray-700">
                    Setează ca adresă implicită
                </label>
            </div>

            <!-- Butoane -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('client.addresses.index') }}" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Anulează
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Actualizează Adresa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
