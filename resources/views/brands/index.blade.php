@extends('layouts.brands_app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">My Brands</h1>
                    <p class="text-gray-600 font-bold">Manage your brand partnerships and campaigns</p>
                </div>
                <a href="{{ route('brands.create') }}" class="bg-gradient-to-r from-purple to-orange text-white px-6 py-3 rounded-2xl font-black uppercase text-sm tracking-widest hover:from-purple-600 hover:to-orange-600 transition-all">
                    Add New Brand
                </a>
            </div>

            @if($brands->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-4 px-4 text-left text-gray-600 font-black uppercase tracking-widest text-xs">Logo</th>
                                <th class="py-4 px-4 text-left text-gray-600 font-black uppercase tracking-widest text-xs">Brand Name</th>
                                <th class="py-4 px-4 text-left text-gray-600 font-black uppercase tracking-widest text-xs">Website</th>
                                <th class="py-4 px-4 text-left text-gray-600 font-black uppercase tracking-widest text-xs">Status</th>
                                <th class="py-4 px-4 text-left text-gray-600 font-black uppercase tracking-widest text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->company_name }}" class="w-12 h-12 object-contain rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-500 font-black">{{ strtoupper(substr($brand->company_name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 font-bold text-gray-900">{{ $brand->company_name }}</td>
                                <td class="py-4 px-4">
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" target="_blank" class="text-purple hover:text-orange transition-colors">
                                            {{ parse_url($brand->website, PHP_URL_HOST) ?: $brand->website }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest 
                                        @if($brand->status === 'active') bg-green-100 text-green-800
                                        @elseif($brand->status === 'inactive') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($brand->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('brands.edit', $brand) }}" class="bg-purple text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-orange transition-colors">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 uppercase italic mb-2">No Brands Yet</h3>
                    <p class="text-gray-400 font-bold italic mb-8">Get started by adding your first brand</p>
                    <a href="{{ route('brands.create') }}" class="inline-block bg-gradient-to-r from-purple to-orange text-white px-8 py-3 rounded-2xl font-black uppercase text-sm tracking-widest hover:from-purple-600 hover:to-orange-600 transition-all">
                        Register Your Brand
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
