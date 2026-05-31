<template>
  <div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <StatsCard
        v-for="s in stats"
        :key="s.label"
        :label="s.label"
        :value="s.value"
        :icon="s.icon"
        :bgColor="s.bgColor"
        :textColor="s.textColor"
        :trend="s.trend"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Leave Balance -->
      <div class="lg:col-span-2">
        <LeaveBalance :leaveTypes="leaveTypes" />
      </div>

      <!-- Upcoming Events -->
      <div
        class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
      >
        <div class="p-6 border-b border-slate-100">
          <h4 class="font-bold text-slate-800">Sắp diễn ra</h4>
        </div>
        <div class="divide-y divide-slate-50">
          <div
            v-for="ev in upcomingEvents"
            :key="ev.title"
            class="p-4 flex gap-4 hover:bg-slate-50 transition-colors"
          >
            <div
              class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex flex-col items-center justify-center shrink-0"
            >
              <span class="text-[10px] font-bold uppercase leading-none">{{
                ev.month
              }}</span>
              <span class="text-lg font-bold leading-none">{{ ev.day }}</span>
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-800">{{ ev.title }}</p>
              <p class="text-xs text-slate-500 mt-0.5">{{ ev.desc }}</p>
            </div>
          </div>
        </div>
        <button
          class="w-full py-4 text-sm text-slate-400 font-medium hover:text-sky-600 border-t border-slate-50 transition-colors"
        >
          Xem tất cả lịch
        </button>
      </div>
    </div>

    <!-- Monthly chart (manager/admin) -->
    <div
      v-if="auth.isManagerOrAdmin"
      class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"
    >
      <h4 class="font-bold text-slate-800 mb-5">
        Thống kê đơn nghỉ theo tháng
      </h4>
      <canvas id="dashboardChart" height="80"></canvas>
    </div>
  </div>
</template>

<script setup>
import StatsCard from "@/components/common/StatsCard.vue";
import LeaveBalance from "@/components/dashboard/LeaveBalance.vue";
import { leaveService } from "@/services/leaveService";
import { useAuthStore } from "@/stores/authStore";
import { Chart, registerables } from "chart.js";
import { computed, nextTick, onMounted, ref } from "vue";
Chart.register(...registerables);

const auth = useAuthStore();

const leaves = ref([]);
const leaveTypes = ref([
  {
    id: 1,
    name: "Phép năm",
    balance: auth.user?.remaining_days ?? 12,
    used: 0,
    colorClass: "bg-sky-500",
  },
  { id: 2, name: "Nghỉ ốm", balance: 5, used: 0, colorClass: "bg-emerald-500" },
  {
    id: 3,
    name: "Nghỉ không lương",
    balance: 3,
    used: 0,
    colorClass: "bg-amber-500",
  },
]);

const upcomingEvents = [
  {
    month: "TH5",
    day: "19",
    title: "Nghỉ bù lễ 30/4",
    desc: "Toàn công ty nghỉ",
  },
  {
    month: "TH6",
    day: "03",
    title: "Team Building Q2",
    desc: "Công ty tổ chức",
  },
];

const todayLeaves = computed(() => {
  if (!leaves.value || leaves.value.length === 0) return 0;

  const today = new Date();
  const todayStr = today.toISOString().split("T")[0]; // YYYY-MM-DD

  return leaves.value.filter((leave) => {
    const start = leave.start_date || leave.startDate;
    const end = leave.end_date || leave.endDate;

    if (!start || !end) return false;

    // Kiểm tra đơn nghỉ có bao gồm hôm nay không
    return start <= todayStr && end >= todayStr;
  }).length;
});

const stats = computed(() => {
  const pending = leaves.value.filter((l) => l.status === "pending").length;
  const approved = leaves.value.filter((l) => l.status === "approved").length;

  if (auth.isEmployee)
    return [
      {
        label: "Phép còn lại",
        value: `${auth.user?.remaining_days ?? 0} ngày`,
        icon: "Wallet",
        bgColor: "bg-sky-50",
        textColor: "text-sky-600",
        trend: null,
      },
      {
        label: "Chờ duyệt",
        value: `${pending} đơn`,
        icon: "Clock",
        bgColor: "bg-amber-50",
        textColor: "text-amber-600",
        trend: null,
      },
      {
        label: "Đã duyệt",
        value: `${approved} đơn`,
        icon: "CircleCheck",
        bgColor: "bg-emerald-50",
        textColor: "text-emerald-600",
        trend: approved > 0 ? 12 : null,
      },
      {
        label: "Tổng đơn",
        value: leaves.value.length,
        icon: "FileText",
        bgColor: "bg-indigo-50",
        textColor: "text-indigo-600",
        trend: null,
      },
    ];

  return [
    {
      label: "Chờ duyệt",
      value: `${pending} đơn`,
      icon: "Clock",
      bgColor: "bg-amber-50",
      textColor: "text-amber-600",
      trend: null,
    },
    {
      label: "Đã duyệt",
      value: `${approved} đơn`,
      icon: "CircleCheck",
      bgColor: "bg-emerald-50",
      textColor: "text-emerald-600",
      trend: null,
    },
    {
      label: "Tổng đơn",
      value: leaves.value.length,
      icon: "FileText",
      bgColor: "bg-sky-50",
      textColor: "text-sky-600",
      trend: 2,
    },
    {
      label: "Nghỉ hôm nay",
      value: `${todayLeaves.value} người`,
      icon: "Calendar",
      bgColor: "bg-indigo-50",
      textColor: "text-indigo-600",
      trend: null,
    },
  ];
});

async function loadData() {
  try {
    const res = await leaveService.list({ per_page: 100 });
    leaves.value = res.data ?? [];
    if (auth.user?.remaining_days !== undefined) {
      leaveTypes.value[0].balance =
        auth.user.remaining_days +
        leaves.value
          .filter((l) => l.type === "annual" && l.status === "approved")
          .reduce((s, l) => s + l.total_days, 0);
      leaveTypes.value[0].used = leaves.value
        .filter((l) => l.type === "annual" && l.status === "approved")
        .reduce((s, l) => s + l.total_days, 0);
    }
  } catch {}
}

function initChart() {
  nextTick(() => {
    const el = document.getElementById("dashboardChart");
    if (!el) return;
    if (el._chart) el._chart.destroy();
    el._chart = new Chart(el, {
      type: "bar",
      data: {
        labels: [
          "T1",
          "T2",
          "T3",
          "T4",
          "T5",
          "T6",
          "T7",
          "T8",
          "T9",
          "T10",
          "T11",
          "T12",
        ],
        datasets: [
          {
            label: "Số đơn",
            data: [4, 6, 3, 8, 5, 7, 2, 9, 4, 6, 3, 5],
            backgroundColor: "rgba(14,165,233,0.12)",
            borderColor: "rgb(14,165,233)",
            borderWidth: 2,
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: "rgba(0,0,0,0.04)" } },
          x: { grid: { display: false } },
        },
      },
    });
  });
}

onMounted(async () => {
  await loadData();
  if (auth.isManagerOrAdmin) initChart();
  if (window.lucide) lucide.createIcons();
});
</script>
