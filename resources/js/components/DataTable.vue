<template>
  <div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <slot name="actions"></slot>
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              {{ column.label }}
            </th>
            <th v-if="hasActions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="loading">
            <td :colspan="columns.length + (hasActions ? 1 : 0)" class="px-6 py-4 text-center">
              <div class="flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>
            </td>
          </tr>
          <tr v-else-if="data.length === 0">
            <td :colspan="columns.length + (hasActions ? 1 : 0)" class="px-6 py-4 text-center text-gray-500">
              No data available
            </td>
          </tr>
          <tr v-else v-for="(row, index) in data" :key="row.id || index" class="hover:bg-gray-50">
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            >
              <slot :name="`cell-${column.key}`" :row="row" :value="getValue(row, column.key)">
                {{ getValue(row, column.key) }}
              </slot>
            </td>
            <td v-if="hasActions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <slot name="row-actions" :row="row">
                <button
                  @click="$emit('edit', row)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="$emit('delete', row)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div v-if="pagination" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
      </div>
      <div class="flex space-x-2">
        <button
          @click="$emit('page-change', pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-4 py-2 border rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button
          @click="$emit('page-change', pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-4 py-2 border rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    title: {
      type: String,
      default: 'Data Table'
    },
    columns: {
      type: Array,
      required: true
    },
    data: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    pagination: {
      type: Object,
      default: null
    },
    hasActions: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    getValue(row, key) {
      return key.split('.').reduce((obj, k) => obj?.[k], row) ?? '';
    }
  }
};
</script>

