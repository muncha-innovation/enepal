@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <!-- Post Content -->
    <div class="bg-white p-8 shadow-md rounded-lg mb-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('posts.index',$business) }}" class="text-blue-500 hover:underline">&larr; Back to Posts</a>
            <div class="text-sm text-gray-600">Published by {{ $post->user->name }} on {{ getFormattedDate($post->created_at) }}</div>
        </div>
        <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>
        @if($post->image)
        <img src="{{ getImage($post->image, 'posts/') }}" alt="Post Image" class="mb-4 rounded-lg w-20">
        @endif
        <p class="text-gray-700 mb-4">{{ $post->short_description }}</p>
        <div class="prose max-w-full mb-4">{!! $post->content !!}</div>
        
        <!-- Post Stats -->
        <div class="border-t pt-4 mt-6">
            <div class="flex items-center space-x-6 text-sm text-gray-600">
                <span><i class="fas fa-comments mr-1"></i> {{ $commentStats['total'] }} Comments</span>
                <span><i class="fas fa-check-circle mr-1 text-green-500"></i> {{ $commentStats['approved'] }} Approved</span>
                @if($commentStats['pending'] > 0)
                    <span><i class="fas fa-clock mr-1 text-yellow-500"></i> {{ $commentStats['pending'] }} Pending</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Comments & Replies</h2>
            <button onclick="loadComments()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>

        <!-- Add Comment Form -->
        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Reply as Business</h3>
            <form id="commentForm" class="space-y-4">
                @csrf
                <div>
                    <textarea id="commentText" name="comment" rows="3" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        placeholder="Write your reply..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-reply mr-1"></i> Reply
                    </button>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        <div id="commentsList">
            <div class="text-center py-8">
                <i class="fas fa-comments text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-600">Loading comments...</p>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Reply to Comment</h3>
        <form id="replyForm">
            @csrf
            <input type="hidden" id="replyToId" name="parent_id">
            <div class="mb-4">
                <textarea id="replyText" name="comment" rows="3" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Write your reply..."></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeReplyModal()" 
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Reply
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
let currentReplyToId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadComments();
    
    // Main comment form
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitComment();
    });
    
    // Reply form
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReply();
    });
});

function loadComments() {
    fetch('{{ route("posts.comments.get", [$business, $post]) }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayComments(data.comments);
        }
    })
    .catch(error => {
        console.error('Error loading comments:', error);
        document.getElementById('commentsList').innerHTML = '<p class="text-red-600 text-center">Error loading comments</p>';
    });
}

function displayComments(comments) {
    const container = document.getElementById('commentsList');
    
    if (comments.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-comments text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-600">No comments yet</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = comments.map(comment => `
        <div class="border-b border-gray-200 py-6" data-comment-id="${comment.id}">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    ${comment.user.profile_picture 
                        ? `<img class="h-8 w-8 rounded-full" src="${comment.user.profile_picture}" alt="${comment.user.name}">` 
                        : `<div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                             <span class="text-sm font-medium text-gray-700">${comment.user.name.charAt(0)}</span>
                           </div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">${comment.user.name}</p>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500">${comment.created_at}</span>
                            <button onclick="openReplyModal(${comment.id})" 
                                class="text-xs text-blue-600 hover:text-blue-800">Reply</button>
                            <button onclick="deleteComment(${comment.id})" 
                                class="text-xs text-red-600 hover:text-red-800">Delete</button>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-700">${comment.comment}</p>
                    
                    ${comment.replies.length > 0 ? `
                        <div class="mt-4 ml-4 space-y-3">
                            ${comment.replies.map(reply => `
                                <div class="flex items-start space-x-3 border-l-2 border-gray-200 pl-3">
                                    <div class="flex-shrink-0">
                                        ${reply.user.profile_picture 
                                            ? `<img class="h-6 w-6 rounded-full" src="${reply.user.profile_picture}" alt="${reply.user.name}">` 
                                            : `<div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center">
                                                 <span class="text-xs font-medium text-gray-700">${reply.user.name.charAt(0)}</span>
                                               </div>`
                                        }
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-medium text-gray-900">${reply.user.name}</p>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">${reply.created_at}</span>
                                                <button onclick="deleteComment(${reply.id})" 
                                                    class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-700">${reply.comment}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function submitComment() {
    const commentText = document.getElementById('commentText').value.trim();
    if (!commentText) return;
    
    fetch('{{ route("posts.comments.store", [$business, $post]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            comment: commentText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commentText').value = '';
            loadComments();
        } else {
            alert('Error adding comment');
        }
    });
}

function openReplyModal(commentId) {
    currentReplyToId = commentId;
    document.getElementById('replyToId').value = commentId;
    document.getElementById('replyModal').classList.remove('hidden');
    document.getElementById('replyModal').classList.add('flex');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyModal').classList.remove('flex');
    document.getElementById('replyText').value = '';
}

function submitReply() {
    const replyText = document.getElementById('replyText').value.trim();
    if (!replyText) return;
    
    fetch('{{ route("posts.comments.store", [$business, $post]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            comment: replyText,
            parent_id: currentReplyToId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReplyModal();
            loadComments();
        } else {
            alert('Error adding reply');
        }
    });
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
                loadComments();
            } else {
                alert('Error deleting comment');
            }
        });
    }
}
</script>
@endpush
