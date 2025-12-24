<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <div v-if="title" class="mb-4">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-600 mt-1">{{ subtitle }}</p>
    </div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'Card',
  props: {
    title: {
      type: String,
      default: ''
    },
    subtitle: {
      type: String,
      default: ''
    }
  }
};
</script>

