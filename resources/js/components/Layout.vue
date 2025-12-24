<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main content -->
    <div class="flex-1 flex flex-col ml-64">
      <!-- Header -->
      <Header />

      <!-- Content area -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <div class="container mx-auto px-6 py-8">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue';
import Header from './Header.vue';
</script>
