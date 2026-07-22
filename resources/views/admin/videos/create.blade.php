@extends('layouts.dashboard')

@section('title', 'Adaugă Video - Administrator')
@section('page-title', 'Adaugă Video')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.videos.store') }}" method="POST">
        @csrf
        @include('admin.videos._form')

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Anulează</a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Adaugă video</button>
        </div>
    </form>
</div>
@endsection
