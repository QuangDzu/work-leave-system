<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100]"
        @click="$emit('update:modelValue', false)"
      ></div>
    </Transition>
    <Transition name="slide-over">
      <div
        v-if="modelValue"
        class="fixed inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl z-[101] flex flex-col"
      >
        <!-- Header -->
        <div
          class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0"
        >
          <h3 class="text-xl font-bold text-slate-800">{{ title }}</h3>
          <button
            @click="$emit('update:modelValue', false)"
            class="p-2 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50"
          >
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>
        </div>

        <!-- Content slot -->
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
          <slot />
        </div>

        <!-- Footer slot -->
        <div
          v-if="$slots.footer"
          class="p-6 border-t border-slate-100 shrink-0"
        >
          <slot name="footer" />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: "" },
});
defineEmits(["update:modelValue"]);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-over-enter-active,
.slide-over-leave-active {
  transition: transform 0.3s ease;
}
.slide-over-enter-from,
.slide-over-leave-to {
  transform: translateX(100%);
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
