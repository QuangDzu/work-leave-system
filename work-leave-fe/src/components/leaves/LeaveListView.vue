<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="flex bg-slate-100 p-1 rounded-2xl">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-white shadow-sm' : ''"
          class="px-5 py-2.5 text-sm font-medium rounded-xl transition-all"
        >
          {{ tab.name }}
        </button>
      </div>
      <button
        @click="$emit('create')"
        class="px-6 py-3 bg-sky-600 text-white rounded-2xl font-semibold flex items-center gap-2 hover:bg-sky-700"
      >
        <i data-lucide="plus" class="w-5 h-5"></i>
        Tạo đơn nghỉ phép
      </button>
    </div>

    <LeaveTable
      :leaves="filteredLeaves"
      :showUser="true"
      @approve="$emit('approve', $event)"
      @reject="$emit('reject', $event)"
      @edit="$emit('edit', $event)"
      @cancel="$emit('cancel', $event)"
    />
  </div>
</template>

<script setup>
import LeaveTable from "./LeaveTable.vue";

defineProps({
  leaves: Array,
  activeTab: String,
  tabs: Array,
});

const emit = defineEmits(["create", "approve", "reject", "edit", "cancel"]);

const filteredLeaves = computed(() => {
  if (!props.activeTab || props.activeTab === "all") return props.leaves;
  return props.leaves.filter((l) => l.status === props.activeTab);
});
</script>
