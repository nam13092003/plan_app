<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
    />
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="helper && !error" class="mt-1 text-sm text-gray-500">{{ helper }}</p>
  </div>
</template>

<script>
export default {
  name: 'FormInput',
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    placeholder: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: ''
    },
    helper: {
      type: String,
      default: ''
    },
    id: {
      type: String,
      default: () => `input-${Math.random().toString(36).substr(2, 9)}`
    }
  },
  emits: ['update:modelValue']
};
</script>

