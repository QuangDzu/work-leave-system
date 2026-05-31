<template>
  <div class="flex h-screen overflow-hidden bg-slate-50">
    <Sidebar />
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <AppHeader />
      <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import AppHeader from "@/components/layout/Header.vue";
import Sidebar from "@/components/layout/Sidebar.vue";
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
