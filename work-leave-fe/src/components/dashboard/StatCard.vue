<template>
  <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
    <div class="flex justify-between items-start">
      <div :class="`p-3 rounded-2xl ${bgColor} ${textColor}`">
        <i :data-lucide="icon" class="w-6 h-6"></i>
      </div>
      <span
        v-if="trend"
        :class="
          trend > 0
            ? 'text-emerald-600 bg-emerald-50'
            : 'text-rose-600 bg-rose-50'
        "
        class="text-xs font-medium px-3 py-1 rounded-full"
      >
        {{ trend > 0 ? "+" : "" }}{{ trend }}%
      </span>
    </div>
    <p class="mt-6 text-slate-500 text-sm font-medium">{{ label }}</p>
    <h3 class="text-3xl font-bold mt-1">{{ value }}</h3>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: String,
  icon: String,
  bgColor: String,
  textColor: String,
  trend: Number,
});
</script>
