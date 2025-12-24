<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <select
      :id="id"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :required="required"
      :disabled="disabled"
      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
    >
      <option value="">{{ placeholder || 'Select an option' }}</option>
      <option
        v-for="option in options"
        :key="getOptionValue(option)"
        :value="getOptionValue(option)"
      >
        {{ getOptionLabel(option) }}
      </option>
    </select>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="helper && !error" class="mt-1 text-sm text-gray-500">{{ helper }}</p>
  </div>
</template>

<script>
export default {
  name: 'FormSelect',
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    options: {
      type: Array,
      required: true
    },
    optionValue: {
      type: String,
      default: 'id'
    },
    optionLabel: {
      type: String,
      default: 'name'
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
      default: () => `select-${Math.random().toString(36).substr(2, 9)}`
    }
  },
  emits: ['update:modelValue'],
  methods: {
    getOptionValue(option) {
      return typeof option === 'object' ? option[this.optionValue] : option;
    },
    getOptionLabel(option) {
      return typeof option === 'object' ? option[this.optionLabel] : option;
    }
  }
};
</script>

