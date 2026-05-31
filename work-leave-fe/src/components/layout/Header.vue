<template>
  <header
    class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0"
  >
    <h1 class="text-lg font-semibold text-slate-800">{{ pageTitle }}</h1>

    <div class="flex items-center gap-3">
      <!-- Search -->
      <div class="relative hidden sm:block">
        <span
          class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"
        >
          <Icon name="Search" class="w-4 h-4" />
        </span>
        <input
          type="text"
          v-model="search"
          placeholder="Tìm kiếm..."
          class="pl-9 pr-4 py-1.5 text-sm bg-slate-100 border-none rounded-full focus:ring-2 focus:ring-sky-500 outline-none w-56"
        />
      </div>

      <!-- Notifications -->
      <button class="p-2 text-slate-400 hover:text-sky-600 relative">
        <Icon name="Bell" class="w-5 h-5" />
        <span
          class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"
        ></span>
      </button>

      <!-- Avatar -->
      <div
        class="w-9 h-9 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold text-sm"
      >
        {{ initials(auth.user?.name) }}
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from "@/stores/authStore";
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import Icon from "../ui/Icon.vue";

const auth = useAuthStore();
const route = useRoute();
const search = ref("");

const titles = {
  Dashboard: "Bảng điều khiển",
  Leaves: "Quản lý đơn nghỉ phép",
  LeaveCreate: "Tạo đơn nghỉ phép",
  LeaveEdit: "Chỉnh sửa đơn nghỉ",
  Users: "Quản lý nhân sự",
  Reports: "Báo cáo & Thống kê",
  Settings: "Cấu hình hệ thống",
};

const pageTitle = computed(() => titles[route.name] || "WorkLeave Hub");

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((w) => w[0])
    .slice(-2)
    .join("")
    .toUpperCase();
}

onMounted(() => {
  if (window.lucide) lucide.createIcons();
});
</script>
