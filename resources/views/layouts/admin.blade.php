@extends('layouts.dashboard')

@section('sidebar')
<ul class="space-y-1 px-3">
    <li>
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    </li>
    
    <li class="pt-4">
        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestionare</span>
    </li>
    
    <li>
        <a href="{{ route('admin.craftsmen') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.craftsmen*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Meseriași
        </a>
    </li>
    
    <li>
        <a href="{{ route('admin.reviews') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.reviews*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            Recenzii
        </a>
    </li>
    
    <li>
        <a href="{{ route('admin.services') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.services*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Servicii
        </a>
    </li>
    
    <li>
        <a href="{{ route('admin.articles.index') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.articles*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            Articole
        </a>
    </li>
    
    <li class="pt-4">
        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Analytics & Rapoarte</span>
    </li>
    
    <li>
        <a href="{{ route('admin.analytics.index') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.analytics*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Analytics
        </a>
    </li>
    
    <li>
        <a href="{{ route('admin.affiliates.index') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.affiliates*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Afiliați
        </a>
    </li>
    
    <li class="pt-4">
        <span class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Setări</span>
    </li>
    
    <li>
        <a href="{{ route('admin.email-templates.index') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.email-templates*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Template-uri Email
        </a>
    </li>
    
    <li>
        <a href="{{ route('admin.profile') }}" 
           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.profile*') ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil
        </a>
    </li>
</ul>
@endsection
