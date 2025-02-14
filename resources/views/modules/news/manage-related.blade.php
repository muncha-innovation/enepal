@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - News Form and Categories -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    @include('modules.shared.success_error')
                    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('modules.news.partials._news_form', ['locations' => $locations])
                    </form>
                </div>
            </div>

            <!-- Right Column - Related News Management -->
            <div class="lg:col-span-1 space-y-6">
                @if($news->isSubNews())
                    @include('modules.news.partials._parent_news_section')
                @endif

                @include('modules.news.partials._sub_news_section')
                @include('modules.news.partials._available_news_section')
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('modules.news.partials._upload_scripts')
    @include('modules.news.partials._tag_scripts')
@endpush
