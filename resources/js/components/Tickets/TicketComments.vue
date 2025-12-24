
<template>
  <div class="space-y-4">
    <!-- New Comment Form -->
    <div class="flex items-start space-x-4">
      <div class="flex-shrink-0">
        <!-- You can replace this with the logged-in user's avatar -->
        <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
          <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 20.993V24H0v-2.993A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </span>
      </div>
      <div class="min-w-0 flex-1">
        <form @submit.prevent="submitComment">
          <div>
            <label for="comment" class="sr-only">Add a comment</label>
            <textarea id="comment" v-model="newComment" rows="3"
                      class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md"
                      placeholder="Add a comment..."></textarea>
          </div>
          <div class="mt-3 flex items-center justify-end">
            <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              Comment
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-6">
      <div v-for="comment in comments" :key="comment.id" class="flex space-x-4">
        <div class="flex-shrink-0">
          <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 20.993V24H0v-2.993A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </span>
        </div>
        <div class="flex-1">
          <div class="bg-gray-100 p-3 rounded-lg">
            <div class="flex items-baseline justify-between">
              <p class="text-sm font-medium text-gray-900">{{ comment.user.name }}</p>
              <p class="text-xs text-gray-500">{{ new Date(comment.created_at).toLocaleString() }}</p>
            </div>
            <p class="mt-1 text-sm text-gray-700">{{ comment.comment }}</p>
          </div>
        </div>
      </div>
       <div v-if="!comments || comments.length === 0" class="text-center text-gray-500 py-4">
        No comments yet.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useTicketsStore } from '@/stores/tickets';

const props = defineProps({
  ticketId: {
    type: [String, Number],
    required: true,
  },
  comments: {
    type: Array,
    default: () => [],
  }
});

const ticketsStore = useTicketsStore();
const newComment = ref('');

onMounted(() => {
  ticketsStore.fetchComments(props.ticketId);
});

const submitComment = async () => {
  if (!newComment.value.trim()) return;
  await ticketsStore.postComment(props.ticketId, { comment: newComment.value });
  newComment.value = '';
};
</script>
