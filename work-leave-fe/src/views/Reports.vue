<template>
  <div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        v-for="s in stats"
        :key="s.label"
        class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm"
      >
        <div class="flex items-center justify-between mb-4">
          <div :class="`p-2 rounded-xl ${s.bg} ${s.color}`">
            <i :data-lucide="s.icon" class="w-5 h-5"></i>
          </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">{{ s.label }}</p>
        <h3 class="text-2xl font-bold mt-1">{{ s.value }}</h3>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Monthly chart -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex justify-between items-center mb-5">
          <h4 class="font-bold text-slate-800">Đơn nghỉ theo tháng</h4>
          <button
            @click="exportExcel"
            class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-colors flex items-center gap-1.5"
          >
            <i data-lucide="download" class="w-3 h-3"></i> Xuất Excel
          </button>
        </div>
        <canvas id="reportsChart" height="200"></canvas>
      </div>

      <!-- By type -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h4 class="font-bold text-slate-800 mb-5">Phân loại theo trạng thái</h4>
        <div class="space-y-4">
          <div v-for="item in byStatus" :key="item.label">
            <div class="flex justify-between text-sm mb-1.5">
              <span class="font-medium text-slate-700">{{ item.label }}</span>
              <span class="text-slate-500">{{ item.count }} đơn</span>
            </div>
            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
              <div
                :style="{
                  width:
                    totalLeaves > 0
                      ? (item.count / totalLeaves) * 100 + '%'
                      : '0%',
                }"
                :class="item.color"
                class="h-full rounded-full transition-all duration-500"
              ></div>
            </div>
          </div>
        </div>

        <div
          v-if="leaves.length > 0"
          class="mt-6 pt-5 border-t border-slate-100"
        >
          <h5 class="text-sm font-bold text-slate-700 mb-3">Đơn gần nhất</h5>
          <div class="space-y-3">
            <div
              v-for="l in leaves.slice(0, 4)"
              :key="l.id"
              class="flex items-center gap-3"
            >
              <div
                class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-bold text-xs shrink-0"
              >
                {{ initials(l.user?.name) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-slate-700 truncate">
                  {{ l.user?.name }}
                </p>
                <p class="text-[10px] text-slate-400">
                  {{ typeLabel(l.type) }} · {{ l.total_days }} ngày
                </p>
              </div>
              <span
                :class="statusStyle(l.status)"
                class="text-[10px] font-bold px-2 py-0.5 rounded-full border uppercase"
              >
                {{ statusLabel(l.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { leaveService } from "@/services/leaveService";
import { Chart, registerables } from "chart.js";
import { computed, nextTick, onMounted, ref } from "vue";
Chart.register(...registerables);

const leaves = ref([]);

const totalLeaves = computed(() => leaves.value.length);

const stats = computed(() => [
  {
    label: "Tổng đơn",
    value: leaves.value.length,
    icon: "file-text",
    bg: "bg-sky-50",
    color: "text-sky-600",
  },
  {
    label: "Đã duyệt",
    value: leaves.value.filter((l) => l.status === "approved").length,
    icon: "check-circle",
    bg: "bg-emerald-50",
    color: "text-emerald-600",
  },
  {
    label: "Chờ duyệt",
    value: leaves.value.filter((l) => l.status === "pending").length,
    icon: "clock",
    bg: "bg-amber-50",
    color: "text-amber-600",
  },
  {
    label: "Từ chối",
    value: leaves.value.filter((l) => l.status === "rejected").length,
    icon: "x-circle",
    bg: "bg-rose-50",
    color: "text-rose-600",
  },
]);

const byStatus = computed(() => [
  {
    label: "Đã duyệt",
    count: leaves.value.filter((l) => l.status === "approved").length,
    color: "bg-emerald-500",
  },
  {
    label: "Chờ duyệt",
    count: leaves.value.filter((l) => l.status === "pending").length,
    color: "bg-amber-500",
  },
  {
    label: "Từ chối",
    count: leaves.value.filter((l) => l.status === "rejected").length,
    color: "bg-rose-500",
  },
  {
    label: "Đã hủy",
    count: leaves.value.filter((l) => l.status === "cancelled").length,
    color: "bg-slate-400",
  },
]);

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((w) => w[0])
    .slice(-2)
    .join("")
    .toUpperCase();
}

function typeLabel(t) {
  return { annual: "Phép năm", sick: "Nghỉ ốm", unpaid: "Không lương" }[t] || t;
}

function statusLabel(s) {
  return (
    {
      new: "Mới",
      pending: "Chờ duyệt",
      approved: "Đã duyệt",
      rejected: "Từ chối",
      cancelled: "Đã hủy",
    }[s] || s
  );
}

function statusStyle(s) {
  return (
    {
      pending: "bg-amber-50 border-amber-200 text-amber-700",
      approved: "bg-emerald-50 border-emerald-200 text-emerald-700",
      rejected: "bg-rose-50 border-rose-200 text-rose-700",
      cancelled: "bg-slate-50 border-slate-200 text-slate-500",
      new: "bg-slate-50 border-slate-200 text-slate-500",
    }[s] || ""
  );
}

function exportExcel() {
  alert("Tính năng xuất Excel đang phát triển.");
}

function initChart() {
  nextTick(() => {
    const el = document.getElementById("reportsChart");
    if (!el) return;
    if (el._chart) el._chart.destroy();
    el._chart = new Chart(el, {
      type: "line",
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
            label: "Đã duyệt",
            data: [3, 4, 2, 6, 4, 5, 2, 7, 3, 5, 2, 4],
            borderColor: "rgb(16,185,129)",
            backgroundColor: "rgba(16,185,129,0.08)",
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 4,
          },
          {
            label: "Từ chối",
            data: [1, 2, 1, 2, 1, 2, 0, 2, 1, 1, 1, 1],
            borderColor: "rgb(239,68,68)",
            backgroundColor: "rgba(239,68,68,0.05)",
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: "bottom", labels: { usePointStyle: true } },
        },
        scales: {
          y: { beginAtZero: true, grid: { color: "rgba(0,0,0,0.04)" } },
          x: { grid: { display: false } },
        },
      },
    });
  });
}

async function loadData() {
  try {
    const res = await leaveService.list({ per_page: 100 });
    leaves.value = res.data ?? [];
  } catch {}
  initChart();
}

onMounted(() => {
  loadData();
  if (window.lucide) lucide.createIcons();
});
</script>
