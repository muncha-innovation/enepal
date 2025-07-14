@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <!-- Header -->
  <div>
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600">Welcome back! Here's an overview of your business activities.</p>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total Businesses -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Total Businesses</dt>
              <dd class="text-lg font-medium text-gray-900">{{ $stats['total_businesses'] }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Posts -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Total Posts</dt>
              <dd class="text-lg font-medium text-gray-900">{{ $stats['total_posts'] }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Comments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Total Comments</dt>
              <dd class="text-lg font-medium text-gray-900">{{ $stats['total_comments'] }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Comments -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Pending Comments</dt>
              <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_comments'] }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Comment Activities Section -->
    <div class="lg:col-span-2">
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900 flex items-center">
            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            Recent Comment Activities
          </h3>
        </div>
        
        <div class="p-6">
          @if($stats['pending_comments'] > 0)
            <!-- Pending Comments Alert -->
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-yellow-800">
                    You have {{ $stats['pending_comments'] }} pending comment{{ $stats['pending_comments'] > 1 ? 's' : '' }}
                  </h3>
                  <p class="mt-1 text-sm text-yellow-700">
                    These comments require your review and approval.
                  </p>
                </div>
              </div>
            </div>
          @endif

          <!-- Pending Comments Section -->
          @if($recentData['pending_comments']->count() > 0)
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Pending Comments</h4>
              <div class="space-y-3">
                @foreach($recentData['pending_comments'] as $comment)
                  <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                    <div class="flex-shrink-0">
                      <img class="h-8 w-8 rounded-full" 
                           src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                           alt="{{ $comment->user->name }}">
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                      </div>
                      <p class="text-sm text-gray-600 mt-1">{{ Str::limit($comment->comment, 100) }}</p>
                      <p class="text-xs text-gray-500 mt-1">
                        On: <a href="{{ route('posts.show', [$comment->post->business, $comment->post]) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->post->title }}</a>
                        <span class="mx-1">•</span>
                        {{ $comment->post->business->name }}
                      </p>
                      <div class="flex items-center space-x-2 mt-2">
                        <button onclick="approveComment({{ $comment->id }}, {{ $comment->post->business_id }})" 
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                          Approve
                        </button>
                        <button onclick="deleteComment({{ $comment->id }}, {{ $comment->post->business_id }})" 
                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Recent Comments Section -->
          @if($recentData['recent_comments']->count() > 0)
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Comments</h4>
              <div class="space-y-3">
                @foreach($recentData['recent_comments'] as $comment)
                  <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                      <img class="h-8 w-8 rounded-full" 
                           src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                           alt="{{ $comment->user->name }}">
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                      </div>
                      <p class="text-sm text-gray-600 mt-1">{{ Str::limit($comment->comment, 100) }}</p>
                      <p class="text-xs text-gray-500 mt-1">
                        On: <a href="{{ route('posts.show', [$comment->post->business, $comment->post]) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->post->title }}</a>
                        <span class="mx-1">•</span>
                        {{ $comment->post->business->name }}
                      </p>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          @if($recentData['recent_comments']->count() === 0 && $recentData['pending_comments']->count() === 0)
            <div class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No comments yet</h3>
              <p class="mt-1 text-sm text-gray-500">Comments from customers will appear here.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Recent Posts -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Posts</h3>
        </div>
        <div class="p-6">
          @if($recentData['recent_posts']->count() > 0)
            <div class="space-y-3">
              @foreach($recentData['recent_posts'] as $post)
                <div class="block hover:bg-gray-50 rounded-lg p-2 -m-2 transition-colors">
                  <div class="flex items-center space-x-3">
                    @if($post->image)
                      <img class="h-10 w-10 rounded-lg object-cover" src="{{ getImage($post->image, 'posts/') }}" alt="">
                    @else
                      <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                      </div>
                    @endif
                    <div class="flex-1 min-w-0">
                      <a href="{{ route('posts.show', [$post->business, $post]) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 truncate block">
                        {{ $post->title }}
                      </a>
                      <p class="text-xs text-gray-500">{{ $post->business->name }} • {{ $post->created_at->diffForHumans() }}</p>
                      <div class="flex items-center space-x-3 mt-1">
                        <a href="{{ route('posts.show', [$post->business, $post]) }}#comments" class="text-xs text-gray-500 hover:text-blue-600 flex items-center">
                          <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                          </svg>
                          {{ $post->comments_count }} comments
                        </a>
                        <span class="text-xs text-gray-500 flex items-center">
                          <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                          </svg>
                          {{ $post->likes_count }} likes
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-sm text-gray-500">No recent posts.</p>
          @endif
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
          <div class="space-y-3">
            @if($businesses->count() > 0)
              @foreach($businesses->take(3) as $business)
                <div class="border rounded-lg p-3">
                  <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $business->name }}</h4>
                  <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('posts.create', $business) }}" class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded hover:bg-blue-100">
                      Add Post
                    </a>
                    <a href="{{ route('posts.index', $business) }}" class="text-xs bg-gray-50 text-gray-700 px-2 py-1 rounded hover:bg-gray-100">
                      View Posts
                    </a>
                  </div>
                </div>
              @endforeach
            @endif
            
            @if($businesses->count() === 0)
              <div class="text-center py-4">
                <p class="text-sm text-gray-500 mb-3">No businesses yet.</p>
                <a href="{{ route('business.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                  Create Business
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
// Comment approval and deletion functions
async function approveComment(commentId, businessId) {
  try {
    const response = await fetch(`/comments/${businessId}/${commentId}/approve`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (response.ok) {
      location.reload();
    } else {
      alert('Error approving comment');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error approving comment');
  }
}

async function deleteComment(commentId, businessId) {
  if (!confirm('Are you sure you want to delete this comment?')) {
    return;
  }
  
  try {
    const response = await fetch(`/comments/${businessId}/${commentId}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (response.ok) {
      location.reload();
    } else {
      alert('Error deleting comment');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error deleting comment');
  }
}
</script>
@endpush
