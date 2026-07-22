@extends('layouts.dashboard')

@section('title', 'Editează Video - Administrator')
@section('page-title', 'Editează Video')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <img src="{{ $video->thumbnail_url }}" alt="" class="w-full max-w-sm rounded-lg mb-6 bg-gray-100">

    <form action="{{ route('admin.videos.update', $video->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.videos._form')

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Anulează</a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Salvează modificările</button>
        </div>
    </form>
</div>
@endsection
