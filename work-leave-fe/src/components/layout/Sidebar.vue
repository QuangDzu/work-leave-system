<template>
  <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
    <!-- Logo -->
    <div class="p-6 border-b border-slate-100">
      <div class="flex items-center gap-2 text-sky-600 font-bold text-xl">
        <div class="p-1.5 bg-sky-100 rounded-lg">
          <Icon name="CalendarCheck" class="w-6 h-6" />
        </div>
        WorkLeave
      </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
      <div
        class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-2"
      >
        Chính
      </div>

      <RouterLink
        :to="{ name: 'Dashboard' }"
        :class="navClass('/dashboard')"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all"
      >
        <LayoutDashboard class="w-5 h-5" /> Dashboard
      </RouterLink>

      <RouterLink
        :to="{ name: 'Leaves' }"
        :class="navClass('/leaves')"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all"
      >
        <FileText class="w-5 h-5" /> Đơn nghỉ phép
        <span
          v-if="pendingCount > 0 && auth.isManagerOrAdmin"
          class="bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none ml-auto"
        >
          {{ pendingCount }}
        </span>
      </RouterLink>

      <!-- Manager + Admin -->
      <template v-if="auth.isManagerOrAdmin">
        <div
          class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-4 mb-2 px-2"
        >
          Quản trị
        </div>

        <RouterLink
          :to="{ name: 'Users' }"
          :class="navClass('/users')"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all"
        >
          <Users class="w-5 h-5" /> Nhân sự
        </RouterLink>

        <!-- Admin only -->
        <template v-if="auth.isAdmin">
          <RouterLink
            :to="{ name: 'Reports' }"
            :class="navClass('/reports')"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all"
          >
            <ChartBar class="w-5 h-5" /> Báo cáo
          </RouterLink>

          <RouterLink
            :to="{ name: 'Settings' }"
            :class="navClass('/settings')"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all"
          >
            <Settings class="w-5 h-5" /> Cấu hình
          </RouterLink>
        </template>
      </template>
    </nav>

    <!-- User info -->
    <div class="p-4 border-t border-slate-100">
      <div class="flex items-center gap-3">
        <div
          class="w-9 h-9 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold text-sm shrink-0"
        >
          {{ initials(auth.user?.name) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-slate-800 truncate">
            {{ auth.user?.name }}
          </p>
          <p class="text-[11px] text-slate-400 uppercase">
            {{ auth.roleName }}
          </p>
        </div>
        <button
          @click="handleLogout"
          title="Đăng xuất"
          class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50"
        >
          <LogOut class="w-4 h-4" />
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from "@/stores/authStore";
import { useLeaveStore } from "@/stores/leaveStore";
import {
  ChartBar,
  FileText,
  LayoutDashboard,
  LogOut,
  Settings,
  Users,
} from "lucide-vue-next";
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import Icon from "../ui/Icon.vue";

const auth = useAuthStore();
const leaveStore = useLeaveStore();
const route = useRoute();
const router = useRouter();

const pendingCount = computed(() => leaveStore.pendingCount);

function navClass(path) {
  const active = route.path.startsWith(path);
  return active
    ? "bg-sky-50 text-sky-600 shadow-sm shadow-sky-50"
    : "text-slate-500 hover:bg-slate-50 hover:text-slate-900";
}

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((w) => w[0])
    .slice(-2)
    .join("")
    .toUpperCase();
}

async function handleLogout() {
  await auth.logout();
  router.push({ name: "Login" });
}

onMounted(() => {
  if (auth.isManagerOrAdmin) leaveStore.fetchPendingCount();
  if (window.lucide) lucide.createIcons();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
