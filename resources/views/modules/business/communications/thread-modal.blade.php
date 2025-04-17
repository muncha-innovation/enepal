<!-- New Thread Modal -->
<div id="newThreadModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
  <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Create New Thread</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.threadManagement.hideNewThreadModal()">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <form id="newThreadForm" onsubmit="window.threadManagement.createNewThread(event)" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="thread_title" class="block text-sm font-medium text-gray-700">Thread Title</label>
          <input type="text" name="title" id="thread_title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., Order Support, Product Question" required>
        </div>
        
        <div>
          <label for="thread_description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
          <textarea name="description" id="thread_description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Briefly describe what this thread is about"></textarea>
        </div>
        
        <div>
          <label for="thread_message" class="block text-sm font-medium text-gray-700">First Message</label>
          <textarea name="message" id="thread_message" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Type your first message for this thread" required></textarea>
        </div>
        
        <div class="flex justify-end pt-2">
          <button type="button" onclick="window.threadManagement.hideNewThreadModal()" class="mr-2 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
          </button>
          <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Create Thread
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
