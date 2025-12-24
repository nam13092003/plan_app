<template>
  <div>
    <h3 class="font-semibold text-gray-700 mb-4">Comments</h3>
    <div class="space-y-4">
      <!-- New Comment Form -->
      <div class="flex items-start">
        <img class="h-8 w-8 rounded-full object-cover" :src="authStore.user?.avatar || 'https://via.placeholder.com/150'" alt="Your Avatar">
        <div class="ml-3 w-full">
          <textarea 
            v-model="newComment"
            class="w-full form-textarea rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
            rows="2" 
            placeholder="Write a comment..."
          ></textarea>
          <button 
            @click="postComment" 
            :disabled="!newComment.trim() || isPosting"
            class="mt-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            {{ isPosting ? 'Posting...' : 'Post Comment' }}
          </button>
        </div>
      </div>

      <!-- Separator -->
      <hr class="my-4">

      <!-- Existing Comments -->
      <div v-if="loading" class="text-center text-gray-500">Loading comments...</div>
      <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
      <ul v-else-if="comments.length" class="space-y-4">
        <li v-for="comment in comments" :key="comment.id" class="flex items-start">
          <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.avatar || `https://ui-avatars.com/api/?name=${comment.user?.name}`" :alt="comment.user?.name">
          <div class="ml-3 bg-gray-100 rounded-lg p-3 w-full">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-900">{{ comment.user?.name }}</p>
              <p class="text-xs text-gray-500">{{ formatTimeAgo(comment.created_at) }}</p>
            </div>
            <p class="text-sm text-gray-700 mt-1">{{ comment.comment }}</p>
          </div>
        </li>
      </ul>
      <div v-else class="text-center py-4 text-gray-500">No comments yet.</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores';
import api from '@/services/api';

const props = defineProps({
  taskId: {
    type: [Number, String],
    required: true,
  }
});

const authStore = useAuthStore();
const comments = ref([]);
const newComment = ref('');
const loading = ref(false);
const isPosting = ref(false);
const error = ref(null);

const fetchComments = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get(`/tickets/${props.taskId}/comments`);
    comments.value = response.data.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } catch (err) {
    error.value = 'Failed to load comments.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const postComment = async () => {
  if (!newComment.value.trim()) return;
  isPosting.value = true;
  try {
    await api.post(`/tickets/${props.taskId}/comments`, {
      comment: newComment.value,
    });
    newComment.value = '';
    await fetchComments(); // Refresh comments list
  } catch (err) {
    alert('Failed to post comment.');
    console.error(err);
  } finally {
    isPosting.value = false;
  }
};

const formatTimeAgo = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.round((now - date) / 1000);
  const minutes = Math.round(seconds / 60);
  const hours = Math.round(minutes / 60);
  const days = Math.round(hours / 24);

  if (seconds < 60) return `${seconds}s ago`;
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  return `${days}d ago`;
};

onMounted(fetchComments);
</script>
