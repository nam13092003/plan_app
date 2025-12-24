<template>
  <div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="font-semibold text-lg text-gray-800 mb-4">
      Activity Timeline
    </h3>

    <div v-if="groupedActivities.length" class="space-y-6">
      <div v-for="group in groupedActivities" :key="group.date">
        <!-- Date -->
        <p class="text-sm font-semibold text-gray-600 mb-2">
          {{ group.date }}
        </p>

        <ul class="space-y-4">
          <li
            v-for="activity in group.activities"
            :key="activity.id"
            class="flex items-start"
          >
            <!-- Icon -->
            <div
              class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center"
            >
              <svg
                class="h-5 w-5 text-blue-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </div>

            <!-- Content -->
            <div class="ml-4 space-y-1">
              <!-- Description -->
              <p
                class="text-sm text-gray-800"
                v-html="activity.description"
              ></p>

              <!-- Task -->
              <p
                class="text-xs"
                :class="!activity.ticket ? 'italic text-gray-400' : ''"
              >
                <span class="font-medium">Task:</span>
                <span class="text-blue-600">
                  {{ activity.ticket?.name || '[Deleted Task]' }}
                </span>
              </p>

              <!-- Project -->
              <p
                class="text-xs"
                :class="!activity.ticket?.project ? 'italic text-gray-400' : ''"
                v-if="activity.ticket"
              >
                <span class="font-medium">Project:</span>
                <span class="text-blue-600">
                  {{ activity.ticket?.project?.name || '[Deleted Project]' }}
                </span>
              </p>

              <!-- Status change -->
              <p
                v-if="activity.old_status && activity.new_status"
                class="text-xs text-gray-500"
              >
                <span
                  class="px-2 py-0.5 rounded text-white text-xs"
                  :style="{ backgroundColor: activity.old_status.color }"
                >
                  {{ activity.old_status.name }}
                </span>

                →
                <span
                  class="px-2 py-0.5 rounded text-white text-xs"
                  :style="{ backgroundColor: activity.new_status.color }"
                >
                  {{ activity.new_status.name }}
                </span>
              </p>

              <!-- Time -->
              <p class="text-xs text-gray-400">
                {{ formatTime(activity.created_at) }}
              </p>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div v-else class="text-center py-4 text-gray-500">
      No recent activity.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  activities: {
    type: Array,
    default: () => [],
  },
})

// Format HH:mm
const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit',
  })
}

const groupedActivities = computed(() => {
  const today = new Date()
  const yesterday = new Date()
  yesterday.setDate(today.getDate() - 1)

  const groups = {}

  // Lọc bỏ activity mà ticket hoặc project bị xóa
  const validActivities = props.activities.filter(
    activity => activity.ticket && activity.ticket.project
  )

  validActivities.forEach((activity) => {
    const activityDate = new Date(activity.created_at)
    let key
    let order

    if (activityDate.toDateString() === today.toDateString()) {
      key = 'Today'
      order = 0
    } else if (activityDate.toDateString() === yesterday.toDateString()) {
      key = 'Yesterday'
      order = 1
    } else {
      key = activityDate.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
      order = 2
    }

    if (!groups[key]) {
      groups[key] = {
        date: key,
        order,
        activities: [],
      }
    }

    groups[key].activities.push(activity)
  })

  return Object.values(groups)
    .sort((a, b) => a.order - b.order)
    .map((group) => ({
      ...group,
      activities: group.activities.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
      ),
    }))
})

</script>
