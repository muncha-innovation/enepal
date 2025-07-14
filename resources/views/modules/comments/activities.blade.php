@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => __('Comment Activities')])

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-comments text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Comments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $commentStats['total_comments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $commentStats['pending_comments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-day text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $commentStats['comments_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-week text-indigo-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $commentStats['comments_this_week'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('comments.index', $business) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-list mr-2"></i>
                View All Comments
            </a>
            <a href="{{ route('posts.index', $business) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-newspaper mr-2"></i>
                Manage Posts
            </a>
        </div>
    </div>

    <!-- Pending Comments -->
    @if($pendingComments->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Comments Pending Review</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($pendingComments as $comment)
            <div class="p-6" id="pending-comment-{{ $comment->id }}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @if($comment->user->profile_picture)
                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $comment->user->profile_picture) }}" alt="{{ $comment->user->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ substr($comment->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">On post: <a href="{{ route('posts.show', [$business, $comment->post]) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->post->title }}</a></p>
                        <p class="mt-2 text-sm text-gray-700">{{ $comment->comment }}</p>
                        <div class="mt-3 flex items-center space-x-3">
                            <button onclick="approveComment({{ $comment->id }})" class="text-sm text-green-600 hover:text-green-800 font-medium">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button onclick="deleteComment({{ $comment->id }})" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                            <a href="{{ route('posts.show', [$business, $comment->post]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>View Post
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Comments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Comments</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentComments as $comment)
            <div class="p-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @if($comment->user->profile_picture)
                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $comment->user->profile_picture) }}" alt="{{ $comment->user->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ substr($comment->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                            <div class="flex items-center space-x-2">
                                @if($comment->is_approved)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">On post: <a href="{{ route('posts.show', [$business, $comment->post]) }}" class="text-blue-600 hover:text-blue-800">{{ $comment->post->title }}</a></p>
                        <p class="mt-2 text-sm text-gray-700">{{ Str::limit($comment->comment, 150) }}</p>
                        <div class="mt-3 flex items-center space-x-3">
                            @if(!$comment->is_approved)
                                <button onclick="approveComment({{ $comment->id }})" class="text-sm text-green-600 hover:text-green-800 font-medium">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                            @endif
                            <a href="{{ route('posts.show', [$business, $comment->post]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-reply mr-1"></i>Reply
                            </a>
                            <a href="{{ route('posts.show', [$business, $comment->post]) }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>View Post
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center">
                <i class="fas fa-comments text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No recent comments</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function approveComment(commentId) {
    if (confirm('Approve this comment?')) {
        fetch(`{{ url('/comments') }}/{{ $business->id }}/${commentId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update the view
            } else {
                alert('Error approving comment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving comment');
        });
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch(`{{ url('/comments') }}/{{ $business->id }}/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide the comment element
                const commentElement = document.getElementById(`pending-comment-${commentId}`);
                if (commentElement) {
                    commentElement.style.display = 'none';
                } else {
                    location.reload();
                }
            } else {
                alert('Error deleting comment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting comment');
        });
    }
}
</script>
@endpush 