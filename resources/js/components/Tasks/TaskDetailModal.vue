<template>
  <!-- Backdrop -->
  <div 
    class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity"
    @click="$emit('close')"
  ></div>

  <!-- Modal Panel -->
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-full max-h-[90vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 border-b">
        <h2 class="text-xl font-bold text-gray-800">{{ task.title }}</h2>
        <button @click="$emit('close')" class="p-2 rounded-full hover:bg-gray-200">
          <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="flex-1 overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Content (Left Column) -->
        <div class="md:col-span-2 space-y-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
            <p class="text-gray-600">{{ task.description }}</p>
          </div>
          <!-- Subtasks Component Placeholder -->
          <div>
             <h3 class="font-semibold text-gray-700 mb-2">Subtasks (2/5)</h3>
             <!-- <SubtaskList :subtasks="task.subtasks" /> -->
             <div class="space-y-2 mt-2">
                <div class="flex items-center"><input type="checkbox" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"> <span class="ml-2 text-gray-800 line-through">Initial research</span></div>
                <div class="flex items-center"><input type="checkbox" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"> <span class="ml-2 text-gray-800 line-through">Create wireframes</span></div>
                <div class="flex items-center"><input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"> <span class="ml-2 text-gray-800">Build UI components</span></div>
             </div>
          </div>
           <!-- Attachments Component Placeholder -->
          <div>
             <h3 class="font-semibold text-gray-700 mb-2">Attachments</h3>
             <!-- <AttachmentList :attachments="task.attachments" /> -->
             <div class="mt-2 p-4 border-2 border-dashed rounded-lg text-center text-gray-500">
                Drag & drop files here
             </div>
          </div>
        </div>

        <!-- Metadata & Activity (Right Column) -->
        <div class="md:col-span-1 space-y-6">
          <!-- General Info -->
          <div class="bg-gray-50 rounded-lg p-4 space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-500">Status</label>
              <p class="font-semibold text-gray-800">{{ task.status }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-500">Assignee</label>
              <div class="flex items-center mt-1">
                <img class="h-8 w-8 rounded-full object-cover" :src="task.assignee.avatar" :alt="task.assignee.name">
                <p class="ml-2 font-semibold text-gray-800">{{ task.assignee.name }}</p>
              </div>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-500">Priority</label>
              <p class="font-semibold" :class="priorityClass">{{ task.priority }}</p>
            </div>
             <div>
              <label class="text-sm font-medium text-gray-500">Due Date</label>
              <p class="font-semibold text-gray-800">{{ task.dueDate }}</p>
            </div>
          </div>
          <!-- Comments/History Tabs -->
          <div>
            <!-- <CommentSection /> -->
            <h3 class="font-semibold text-gray-700 mb-2">Comments</h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <img class="h-8 w-8 rounded-full object-cover" :src="task.assignee.avatar" alt="User">
                    <div class="ml-3 bg-gray-100 rounded-lg p-3 w-full">
                        <p class="text-sm text-gray-800">Can we get this done by tomorrow?</p>
                    </div>
                </div>
                <textarea class="w-full form-textarea rounded-md border-gray-300" rows="2" placeholder="Write a comment..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SubtaskList from './SubtaskList.vue';
import AttachmentList from './AttachmentList.vue';
import CommentSection from './CommentSection.vue';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
  show: {
    type: Boolean,
    default: false,
  }
});

defineEmits(['close']);

const priorityClass = computed(() => {
  const priorities = {
    High: 'text-red-600',
    Medium: 'text-yellow-600',
    Low: 'text-green-600'
  };
  return priorities[props.task.priority] || 'text-gray-600';
});
</script>
