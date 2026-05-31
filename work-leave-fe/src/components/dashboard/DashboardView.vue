<template>
  <div class="space-y-8">
    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard
        v-for="stat in stats"
        :key="stat.label"
        :label="stat.label"
        :value="stat.value"
        :icon="stat.icon"
        :bgColor="stat.bgColor"
        :textColor="stat.textColor"
        :trend="stat.trend"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Leave Balance -->
      <LeaveBalanceCard :leaveTypes="leaveTypes" />

      <!-- Upcoming Events -->
      <div
        class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden lg:col-span-1"
      >
        <div class="p-6 border-b">
          <h3 class="font-semibold text-lg">Sắp diễn ra</h3>
        </div>
        <div class="divide-y">
          <div
            v-for="event in upcomingEvents"
            :key="event.title"
            class="p-6 hover:bg-slate-50 transition-colors flex gap-5"
          >
            <div
              class="w-14 h-14 bg-sky-50 rounded-2xl flex flex-col items-center justify-center shrink-0"
            >
              <span class="text-xs font-bold text-sky-600">{{
                event.month
              }}</span>
              <span class="text-2xl font-bold text-sky-700">{{
                event.day
              }}</span>
            </div>
            <div>
              <p class="font-semibold">{{ event.title }}</p>
              <p class="text-sm text-slate-500 mt-1">{{ event.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import LeaveBalanceCard from "./LeaveBalance.vue";
import StatCard from "./StatCard.vue";

defineProps({
  stats: Array,
  leaveTypes: Array,
  upcomingEvents: Array,
});
</script>
