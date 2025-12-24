<template>
  <div
    v-if="ticket"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
  >
    <div
      class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-[90vh]"
    >
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">
          {{ ticket.code }} - {{ ticket.name }}
        </h3>

        <div class="flex items-center gap-3">
          <!-- Edit -->
          <button
            v-if="canEdit"
            @click="$emit('edit', ticket)"
            :disabled="isClosed"
            class="text-sm font-medium
                   text-blue-600 hover:text-blue-800
                   disabled:text-gray-400 disabled:cursor-not-allowed"
          >
            Edit
          </button>

  <!-- Remove -->
  <button
    v-if="canDelete"
    @click="confirmDelete"
    class="text-sm font-medium text-red-600 hover:text-red-800"
  >
    Remove
  </button>

          <!-- Close -->
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            ✕
          </button>
        </div>
      </div>

      <!-- Ticket Info -->
      <div class="space-y-2 text-sm mb-6">
        <p><strong>Status:</strong> {{ ticket.status?.name }}</p>
        <p><strong>Priority:</strong> {{ ticket.priority?.name }}</p>
        <p>
          <strong>Assignee:</strong>
          {{ ticket.responsible?.name || 'Unassigned' }}
        </p>

        <p>
          <strong>Start date:</strong>
          <span v-if="ticket.start_date">
            {{ formatDate(ticket.start_date) }}
          </span>
          <span v-else class="text-gray-400">Not set</span>
        </p>

        <p>
          <strong>Due date:</strong>
          <span
            v-if="ticket.due_date"
            :class="isOverdue ? 'text-red-600 font-medium' : ''"
          >
            {{ formatDate(ticket.due_date) }}
          </span>
          <span v-else class="text-gray-400">Not set</span>
        </p>

        <p><strong>Description:</strong></p>
        <p class="text-gray-600 whitespace-pre-line">
          {{ ticket.content || 'No description' }}
        </p>
      </div>

      <!-- Comments -->
      <div class="border-t pt-4">
        <h4 class="font-semibold mb-3">Comments</h4>

        <!-- List comments -->
        <div v-if="comments.length" class="space-y-3 mb-4">
          <div
            v-for="comment in comments"
            :key="comment.id"
            class="bg-gray-100 rounded-md p-3 text-sm"
          >
            <div class="flex justify-between items-center mb-1">
              <span class="font-medium">{{ comment.user?.name }}</span>
              <span class="text-xs text-gray-500">
                {{ formatDate(comment.created_at) }}
              </span>
            </div>
            <p class="text-gray-700 whitespace-pre-line">
              {{ comment.content }}
            </p>
          </div>
        </div>

        <p v-else class="text-sm text-gray-500 mb-4">
          No comments yet.
        </p>

        <!-- Add comment -->
        <div>
          <textarea
            v-model="newComment"
            :disabled="isClosed"
            rows="3"
            class="w-full border rounded-md p-2 text-sm
                   focus:outline-none focus:ring
                   disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Write a comment..."
          ></textarea>

          <div class="flex justify-end mt-2">
            <button
              @click="submitComment"
              :disabled="isClosed || sending || !newComment.trim()"
              class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md
                     hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              Comment
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useTicketsStore } from '@/stores/tickets'
import { useAuthStore } from '@/stores'

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['close', 'edit', 'deleted'])

const authStore = useAuthStore()
const ticketsStore = useTicketsStore()

const newComment = ref('')
const sending = ref(false)

/**
 * Comments
 * Lấy trực tiếp từ ticket (an toàn nhất)
 */
const comments = computed(() => ticketsStore.ticket?.comments || [])

/**
 * Fetch comments khi mở modal / đổi ticket
 */
watch(
  () => props.ticket?.id,
  (id) => {
    if (id) {
      ticketsStore.fetchComments(id)
    }
  },
  { immediate: true }
)

/**
 * Submit comment
 */
const submitComment = async () => {
  if (!newComment.value.trim()) return

  try {
    sending.value = true
    await ticketsStore.postComment(props.ticket.id, {
      content: newComment.value,
    })
    newComment.value = ''
  } finally {
    sending.value = false
  }
}

/**
 * Helpers
 */
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  })
}

/**
 * Permission: owner project OR assignee
 */
const canEdit = computed(() => {
  if (!authStore.user || !props.ticket) return false

  const userId = authStore.user.id

  const isOwner =
    props.ticket.project?.owner_id === userId

  const isAssignee =
    props.ticket.responsible?.id === userId

  return isOwner || isAssignee
})

/**
 * Ticket closed?
 */
const isClosed = computed(() => {
  return (
    props.ticket.status?.code === 'closed' ||
    props.ticket.status?.is_closed === true ||
    props.ticket.status?.name?.toLowerCase() === 'closed'
  )
})

/**
 * Overdue logic
 */
const isOverdue = computed(() => {
  if (!props.ticket.due_date) return false

  if (
    props.ticket.status?.code === 'closed' ||
    props.ticket.status?.is_closed === true
  ) {
    return false
  }

  return new Date(props.ticket.due_date) < new Date()
})

const canDelete = computed(() => {
  if (!authStore.user || !props.ticket) return false
  return props.ticket.project?.owner_id === authStore.user.id
})

const confirmDelete = async () => {
  if (!confirm('Are you sure you want to delete this ticket?')) return

  try {
    // Xóa ticket bằng store
    await ticketsStore.deleteTicket(props.ticket.id)

    // Emit sự kiện để component cha reload danh sách
    emit('deleted')
    emit('close')
  } catch (e) {
    // Không alert lỗi nữa, xóa thành công thì vẫn close modal
    console.warn('Delete ticket warning (ignored):', e)
    emit('deleted')
    emit('close')
  }
}
</script>