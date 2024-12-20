<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ $news->exists ? route('admin.news.update', $news) : route('admin.news.store') }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf
        @if($news->exists)
            @method('PUT')
        @endif
        
        @include('modules.news.partials._news_form')
    </form>
</div>