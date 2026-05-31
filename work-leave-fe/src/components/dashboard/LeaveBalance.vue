<template>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-6 border-b border-slate-100">
      <h4 class="font-bold text-slate-800">Quỹ nghỉ phép năm</h4>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div
        v-for="lt in leaveTypes"
        :key="lt.id"
        class="p-4 border border-slate-100 rounded-xl bg-slate-50/50"
      >
        <div class="flex justify-between items-end mb-2">
          <span class="text-sm font-semibold text-slate-600">{{
            lt.name
          }}</span>
          <span class="text-xs text-slate-400">
            Còn: <b>{{ Math.max(0, lt.balance - lt.used) }}</b
            >/{{ lt.balance }}
          </span>
        </div>
        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
          <div
            :style="{
              width: Math.min((lt.used / lt.balance) * 100, 100) + '%',
            }"
            :class="lt.colorClass"
            class="h-full rounded-full transition-all duration-500"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  leaveTypes: {
    type: Array,
    default: () => [],
  },
});
</script>
