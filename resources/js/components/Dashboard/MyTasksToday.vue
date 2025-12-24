<template>
  <div class="bg-white p-6 rounded-lg shadow-md">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg text-gray-800">
        Upcoming Tasks (Next 3 Days)
      </h3>
    </div>

    <!-- Task List -->
    <ul v-if="filteredTasks.length" class="space-y-4">
      <li
        v-for="task in filteredTasks"
        :key="task.id"
        class="flex items-start justify-between"
      >
        <div class="space-y-1">
          <!-- Task title -->
          <p class="text-sm font-medium text-gray-900">
            {{ task.title }}
          </p>

          <!-- Project name -->
          <p class="text-xs text-gray-500">
            Project:
            <span class="font-medium text-gray-700">
              {{ task.project?.name || 'No project' }}
            </span>
          </p>

          <!-- Due date -->
          <p class="text-xs text-gray-400">
            Due {{ formatDate(task.due_date) }}
          </p>
        </div>

        <!-- Status Badge -->
        <span
          class="text-xs px-2 py-1 rounded-full whitespace-nowrap"
          :class="statusClass(task)"
        >
          {{ task.status }}
        </span>
      </li>
    </ul>

    <!-- No tasks -->
    <div v-else class="text-center py-6 text-gray-500">
      🎉 No tasks due in the next 3 days.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
})

// --- Format ngày ---
const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

// --- Class status ---
const statusClass = (task) => {
  if (task.is_overdue) return 'bg-red-100 text-red-700'
  if (task.is_due_today) return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-600'
}

// --- Lọc tasks 3 ngày tới và status = "Todo" ---
const filteredTasks = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const threeDaysLater = new Date(today)
  threeDaysLater.setDate(today.getDate() + 3)
  threeDaysLater.setHours(23, 59, 59, 999)

  return props.tasks.filter((task) => {
    if (!task.due_date) return false
    if (task.status !== 'Todo') return false  // chỉ lấy task Todo

    const due = new Date(task.due_date)
    due.setHours(0, 0, 0, 0)

    return due >= today && due <= threeDaysLater
  })
})
</script>
