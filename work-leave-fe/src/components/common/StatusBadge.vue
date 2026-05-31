<template>
  <span
    :class="styles[status]"
    class="px-3 py-1 rounded-full text-[11px] font-bold border inline-flex items-center gap-1.5 uppercase tracking-wide"
  >
    <span class="w-1.5 h-1.5 rounded-full" :class="dots[status]"></span>
    {{ labels[status] || status }}
  </span>
</template>

<script setup>
defineProps({ status: { type: String, required: true } });

const labels = {
  new: "Mới",
  pending: "Chờ duyệt",
  approved: "Đã duyệt",
  rejected: "Từ chối",
  cancelled: "Đã hủy",
};

const styles = {
  new: "bg-slate-50 border-slate-100 text-slate-500",
  pending: "bg-amber-50 border-amber-100 text-amber-700",
  approved: "bg-emerald-50 border-emerald-100 text-emerald-700",
  rejected: "bg-rose-50 border-rose-100 text-rose-700",
  cancelled: "bg-slate-50 border-slate-100 text-slate-400",
};

const dots = {
  new: "bg-slate-400",
  pending: "bg-amber-500",
  approved: "bg-emerald-500",
  rejected: "bg-rose-500",
  cancelled: "bg-slate-400",
};
</script>
