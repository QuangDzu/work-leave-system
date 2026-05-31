<template>
  <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div :class="`p-2 rounded-xl ${bgColor} ${textColor}`">
        <Icon :name="icon" class="w-5 h-5" />
      </div>
      <span
        v-if="trend !== null && trend !== undefined"
        :class="
          trend > 0
            ? 'text-emerald-600 bg-emerald-50'
            : 'text-rose-600 bg-rose-50'
        "
        class="text-xs font-medium px-2 py-0.5 rounded-full"
      >
        {{ trend > 0 ? "+" : "" }}{{ trend }}%
      </span>
    </div>
    <p class="text-slate-500 text-sm font-medium">{{ label }}</p>
    <h3 class="text-2xl font-bold mt-1">{{ value }}</h3>
  </div>
</template>

<script setup>
import Icon from "../ui/Icon.vue";

defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  icon: { type: String, default: "MessageCircleQuestionMark" },
  bgColor: { type: String, default: "bg-sky-50" },
  textColor: { type: String, default: "text-sky-600" },
  trend: { type: Number, default: null },
});
</script>
