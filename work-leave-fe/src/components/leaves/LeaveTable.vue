<template>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-slate-50/50 border-b border-slate-100">
          <th
            v-if="showUser"
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider"
          >
            Nhân viên
          </th>
          <th
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider"
          >
            Thông tin đơn
          </th>
          <th
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider"
          >
            Thời gian
          </th>
          <th
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider"
          >
            Số ngày
          </th>
          <th
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider"
          >
            Trạng thái
          </th>
          <th
            class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right"
          >
            Hành động
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-if="!leaves.length">
          <td
            :colspan="showUser ? 6 : 5"
            class="px-6 py-12 text-center text-slate-400"
          >
            <Icon name="Inbox" class="w-10 h-10 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Không có đơn nghỉ phép nào</p>
          </td>
        </tr>
        <tr
          v-for="leave in leaves"
          :key="leave.id"
          class="hover:bg-slate-50 transition-colors group"
        >
          <!-- Employee column (manager/admin) -->
          <td v-if="showUser" class="px-6 py-5">
            <div class="flex items-center gap-3">
              <div
                class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm shrink-0"
              >
                {{ initials(leave.user?.name) }}
              </div>
              <div>
                <p class="text-sm font-bold text-slate-800">
                  {{ leave.user?.name }}
                </p>
                <p class="text-xs text-slate-500 truncate max-w-[120px]">
                  {{ leave.user?.email }}
                </p>
              </div>
            </div>
          </td>

          <!-- Leave info -->
          <td class="px-6 py-5">
            <span
              :class="typeStyle(leave.type)"
              class="px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wide"
            >
              {{ typeLabel(leave.type) }}
            </span>
            <p
              class="text-xs text-slate-400 mt-1.5 max-w-[160px] truncate"
              :title="leave.reason"
            >
              {{ leave.reason }}
            </p>
          </td>

          <td class="px-6 py-5">
            <p class="text-sm font-medium text-slate-700">
              {{ fmt(leave.start_date) }} → {{ fmt(leave.end_date) }}
            </p>
          </td>

          <td class="px-6 py-5 text-sm font-bold text-slate-700">
            {{ leave.total_days }} ngày
          </td>

          <td class="px-6 py-5">
            <StatusBadge :status="leave.status" />

            <!-- Lý do từ chối -->
            <div
              v-if="leave.status === 'rejected' && leave.rejection_reason"
              class="flex items-start gap-1.5 max-w-[180px] group/reason cursor-default"
            >
              <Icon name="Info" class="w-3 h-3 text-rose-400 shrink-0 mt-0.5" />
              <p
                class="text-[11px] text-rose-500 leading-relaxed line-clamp-2 group-hover/reason:line-clamp-none transition-all"
                :title="leave.rejection_reason"
              >
                {{ leave.rejection_reason }}
              </p>
            </div>
          </td>

          <td class="px-6 py-5 text-right w-36">
            <div class="flex justify-end items-center gap-1.5">
              <!-- Duyệt -->
              <button
                v-if="
                  auth.hasPermission('leave.approve') &&
                  leave.status === 'pending'
                "
                @click="$emit('approve', leave.id)"
                @click.stop="$emit('approve', leave.id)"
                class="p-2.5 text-emerald-600 hover:bg-emerald-50 rounded-xl hover:scale-110 transition-all"
                title="Duyệt"
              >
                <Icon name="CircleCheck" class="w-5 h-5" />
              </button>

              <!-- Từ chối -->
              <button
                v-if="
                  auth.hasPermission('leave.approve') &&
                  leave.status === 'pending'
                "
                @click="$emit('reject', leave)"
                @click.stop="$emit('reject', leave.id)"
                class="p-2.5 text-rose-600 hover:bg-rose-50 rounded-xl hover:scale-110 transition-all"
                title="Từ chối"
              >
                <Icon name="CircleX" class="w-5 h-5" />
              </button>

              <!-- Sửa -->
              <button
                v-if="canEdit(leave)"
                @click="$emit('edit', leave)"
                @click.stop="$emit('edit', leave)"
                class="p-2.5 text-sky-600 hover:bg-sky-50 rounded-xl hover:scale-110 transition-all"
                title="Sửa"
              >
                <Icon name="Pencil" class="w-5 h-5" />
              </button>

              <!-- Hủy -->
              <button
                v-if="canCancel(leave)"
                @click.stop="$emit('cancel', leave.id)"
                class="p-2.5 text-amber-600 hover:bg-amber-50 rounded-xl hover:scale-110 transition-all"
                title="Hủy"
              >
                <Icon name="Ban" class="w-5 h-5" />
              </button>

              <button
                class="p-2.5 text-slate-300 hover:text-slate-400 transition-colors"
              >
                <Icon name="Ellipsis" class="w-5 h-5" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import StatusBadge from "@/components/common/StatusBadge.vue";
import { useAuthStore } from "@/stores/authStore";
import Icon from "../ui/Icon.vue";

const auth = useAuthStore();

const props = defineProps({
  leaves: { type: Array, default: () => [] },
  showUser: { type: Boolean, default: false },
});

defineEmits(["approve", "reject", "edit", "cancel"]);

const canApprove = auth.isManagerOrAdmin;

function canEdit(leave) {
  return (
    ["new", "pending"].includes(leave.status) &&
    (auth.user?.id === leave.user_id || auth.hasPermission("leave.edit"))
  );
}

function canCancel(leave) {
  return (
    ["new", "pending"].includes(leave.status) && auth.user?.id === leave.user_id
  );
}

function fmt(d) {
  if (!d) return "-";
  return new Date(d).toLocaleDateString("vi-VN");
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

function typeLabel(t) {
  return { annual: "Phép năm", sick: "Nghỉ ốm", unpaid: "Không lương" }[t] || t;
}

function typeStyle(t) {
  return (
    {
      annual: "bg-sky-50 border-sky-100 text-sky-700",
      sick: "bg-rose-50 border-rose-100 text-rose-700",
      unpaid: "bg-amber-50 border-amber-100 text-amber-700",
    }[t] || "bg-slate-50 border-slate-100 text-slate-600"
  );
}
</script>
