<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        :id="id"
        type="text"
        :value="searchTerm"
        @input="handleSearch"
        @focus="showDropdown = true"
        @blur="handleBlur"
        :placeholder="placeholder || 'Type to search...'"
        :required="required"
        :disabled="disabled"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
        autocomplete="off"
      />
      <div
        v-if="showDropdown && filteredOptions.length > 0"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <div
          v-for="option in filteredOptions"
          :key="getOptionValue(option)"
          @mousedown.prevent="selectOption(option)"
          class="px-3 py-2 hover:bg-blue-50 cursor-pointer"
          :class="{ 'bg-blue-100': getOptionValue(option) === modelValue }"
        >
          {{ getOptionLabel(option) }}
        </div>
      </div>
      <div
        v-if="showDropdown && filteredOptions.length === 0 && searchTerm"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg px-3 py-2 text-gray-500"
      >
        No results found
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="helper && !error" class="mt-1 text-sm text-gray-500">{{ helper }}</p>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  name: 'FormSelectSearchable',
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
      default: () => `select-searchable-${Math.random().toString(36).substr(2, 9)}`
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const searchTerm = ref('');
    const showDropdown = ref(false);

    const selectedOption = computed(() => {
      if (!props.modelValue) return null;
      return props.options.find(opt => {
        const value = typeof opt === 'object' ? opt[props.optionValue] : opt;
        return value == props.modelValue;
      });
    });

    const displayValue = computed(() => {
      if (selectedOption.value) {
        return typeof selectedOption.value === 'object' 
          ? selectedOption.value[props.optionLabel] 
          : selectedOption.value;
      }
      return searchTerm.value;
    });

    const filteredOptions = computed(() => {
      if (!searchTerm.value) {
        return props.options;
      }
      const term = searchTerm.value.toLowerCase();
      return props.options.filter(option => {
        const label = typeof option === 'object' 
          ? option[props.optionLabel] 
          : option;
        return label.toLowerCase().includes(term);
      });
    });

    const handleSearch = (event) => {
      searchTerm.value = event.target.value;
      showDropdown.value = true;
    };

    const selectOption = (option) => {
      const value = typeof option === 'object' ? option[props.optionValue] : option;
      emit('update:modelValue', value);
      searchTerm.value = typeof option === 'object' ? option[props.optionLabel] : option;
      showDropdown.value = false;
    };

    const handleBlur = () => {
      // Delay to allow click event to fire
      setTimeout(() => {
        showDropdown.value = false;
        // Reset search term to selected value if not selecting
        if (selectedOption.value) {
          searchTerm.value = typeof selectedOption.value === 'object' 
            ? selectedOption.value[props.optionLabel] 
            : selectedOption.value;
        }
      }, 200);
    };

    watch(() => props.modelValue, (newValue) => {
      if (newValue && selectedOption.value) {
        searchTerm.value = typeof selectedOption.value === 'object' 
          ? selectedOption.value[props.optionLabel] 
          : selectedOption.value;
      } else if (!newValue) {
        searchTerm.value = '';
      }
    }, { immediate: true });

    const getOptionValue = (option) => {
      return typeof option === 'object' ? option[props.optionValue] : option;
    };

    const getOptionLabel = (option) => {
      return typeof option === 'object' ? option[props.optionLabel] : option;
    };

    return {
      searchTerm,
      showDropdown,
      filteredOptions,
      displayValue,
      handleSearch,
      selectOption,
      handleBlur,
      getOptionValue,
      getOptionLabel
    };
  }
};
</script>

