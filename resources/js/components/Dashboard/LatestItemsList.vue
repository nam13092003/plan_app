
<template>
  <div class="flow-root">
    <ul role="list" class="-my-5 divide-y divide-gray-200">
      <li v-for="item in items" :key="item.id" class="py-3">
        <div class="flex items-center space-x-4">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ item.name }}
            </p>
            <p v-if="itemType === 'ticket'" class="text-sm text-gray-500 truncate">
              in {{ item.project?.name }}
            </p>
             <p v-if="itemType === 'project'" class="text-sm text-gray-500 truncate">
              Status: {{ item.status?.name }}
            </p>
            <p v-if="itemType === 'activity' || itemType === 'comment'" class="text-sm text-gray-500 truncate">
              by {{ item.user?.name }} on {{ new Date(item.created_at).toLocaleDateString() }}
            </p>
          </div>
          <div v-if="itemType === 'project' || itemType === 'ticket'">
            <router-link 
              :to="getLink(item)"
              class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"
            >
              View
            </router-link>
          </div>
        </div>
      </li>
       <li v-if="!items || items.length === 0">
        <p class="text-sm text-gray-500">No items to display.</p>
      </li>
    </ul>
  </div>
</template>

<script setup>
const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  itemType: {
    type: String,
    required: true,
    validator: (value) => ['project', 'ticket', 'activity', 'comment'].includes(value),
  },
});

const getLink = (item) => {
  if (props.itemType === 'project') {
    return { name: 'ProjectDetail', params: { id: item.id } };
  }
  if (props.itemType === 'ticket') {
    // Assuming you have a TicketDetail route
    // return { name: 'TicketDetail', params: { id: item.id } };
    // For now, link to the project
     return { name: 'ProjectDetail', params: { id: item.project_id } };
  }
  return '#';
};
</script>
