@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('CRM') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Manage your leads and customer relationships') }}</p>
        </div>
        <a href="{{ route('agent.crm.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
            {{ __('Add New Lead') }}
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Leads') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_leads'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Converted') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['converted_leads'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_leads'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Follow-ups') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['follow_ups'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search leads...') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>{{ __('New') }}</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                    <option value="interested" {{ request('status') == 'interested' ? 'selected' : '' }}>{{ __('Interested') }}</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>{{ __('Converted') }}</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>{{ __('Lost') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Source') }}</label>
                <select name="source" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">{{ __('All Sources') }}</option>
                    <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>{{ __('Website') }}</option>
                    <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>{{ __('Referral') }}</option>
                    <option value="social" {{ request('source') == 'social' ? 'selected' : '' }}>{{ __('Social Media') }}</option>
                    <option value="phone" {{ request('source') == 'phone' ? 'selected' : '' }}>{{ __('Phone Call') }}</option>
                    <option value="email" {{ request('source') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-300">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Leads Table -->
    @if(isset($leads) && count($leads) > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Contact') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Source') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Last Contact') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $lead->company ?? '' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $lead->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $lead->phone ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($lead->status == 'new') bg-blue-100 text-blue-800
                                        @elseif($lead->status == 'contacted') bg-yellow-100 text-yellow-800
                                        @elseif($lead->status == 'interested') bg-purple-100 text-purple-800
                                        @elseif($lead->status == 'converted') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($lead->source) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $lead->last_contact_at ? $lead->last_contact_at->diffForHumans() : __('Never') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('agent.crm.show', $lead) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        {{ __('View') }}
                                    </a>
                                    <a href="{{ route('agent.crm.edit', $lead) }}" class="text-gray-600 hover:text-gray-900">
                                        {{ __('Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leads->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Leads Found') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('Start building your pipeline by adding your first lead.') }}</p>
            <a href="{{ route('agent.crm.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                {{ __('Add Your First Lead') }}
            </a>
        </div>
    @endif
</div>
@endsection
