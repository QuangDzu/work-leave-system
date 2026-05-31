import { leaveService } from "@/services/leaveService";
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useLeaveStore = defineStore("leave", () => {
  // ── State ──
  const leaves = ref([]);
  const currentLeave = ref(null);
  const pendingCount = ref(0);
  const loading = ref(false);
  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  });

  // ── Computed ──
  const approvedLeaves = computed(() =>
    leaves.value.filter((l) => l.status === "approved"),
  );
  const pendingLeaves = computed(() =>
    leaves.value.filter((l) => l.status === "pending"),
  );
  const rejectedLeaves = computed(() =>
    leaves.value.filter((l) => l.status === "rejected"),
  );
  const cancelledLeaves = computed(() =>
    leaves.value.filter((l) => l.status === "cancelled"),
  );

  const totalDaysApproved = computed(() =>
    approvedLeaves.value.reduce((sum, l) => sum + (l.total_days ?? 0), 0),
  );

  // ── Actions ──

  /**
   * Tải danh sách đơn nghỉ có filter + phân trang
   */
  async function fetchLeaves(params = {}) {
    loading.value = true;
    try {
      const res = await leaveService.list(params);
      leaves.value = res.data ?? [];
      meta.value = res.meta ?? meta.value;
      return res;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Tải chi tiết 1 đơn
   */
  async function fetchLeave(id) {
    loading.value = true;
    try {
      const res = await leaveService.get(id);
      currentLeave.value = res.data;
      return res.data;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Tạo đơn mới
   */
  async function createLeave(data) {
    const res = await leaveService.create(data);
    // Thêm vào đầu danh sách nếu đang có
    if (leaves.value.length) {
      leaves.value.unshift(res.data);
    }
    return res;
  }

  /**
   * Cập nhật đơn
   */
  async function updateLeave(id, data) {
    const res = await leaveService.update(id, data);
    _updateInList(res.data);
    return res;
  }

  /**
   * Xóa đơn
   */
  async function deleteLeave(id) {
    const res = await leaveService.delete(id);
    leaves.value = leaves.value.filter((l) => l.id !== id);
    return res;
  }

  /**
   * Duyệt đơn
   */
  async function approveLeave(id) {
    const res = await leaveService.approve(id);
    _updateInList(res.data);
    decrement();
    return res;
  }

  /**
   * Từ chối đơn kèm lý do (gửi tới PUT /leaves/{id}/reject)
   */
  async function rejectLeave(id, rejectionReason) {
    const res = await leaveService.reject(id, rejectionReason);
    _updateInList(res.data);
    decrement();
    return res;
  }

  /**
   * Hủy đơn
   */
  async function cancelLeave(id) {
    const res = await leaveService.cancel(id);
    _updateInList(res.data);
    return res;
  }

  /**
   * Lấy số đơn đang chờ duyệt (dùng cho badge sidebar)
   */
  async function fetchPendingCount() {
    try {
      const res = await leaveService.list({ status: "pending", per_page: 1 });
      pendingCount.value = res.meta?.total ?? 0;
    } catch {
      pendingCount.value = 0;
    }
  }

  /**
   * Giảm pending count (sau khi approve hoặc reject)
   */
  function decrement() {
    if (pendingCount.value > 0) pendingCount.value--;
  }

  /**
   * Reset toàn bộ state
   */
  function reset() {
    leaves.value = [];
    currentLeave.value = null;
    pendingCount.value = 0;
    loading.value = false;
    meta.value = { current_page: 1, last_page: 1, per_page: 15, total: 0 };
  }

  // ── Private helpers ──

  /**
   * Cập nhật 1 item trong list sau khi approve/reject/cancel
   */
  function _updateInList(updated) {
    if (!updated?.id) return;
    const idx = leaves.value.findIndex((l) => l.id === updated.id);
    if (idx !== -1) leaves.value[idx] = updated;
    if (currentLeave.value?.id === updated.id) currentLeave.value = updated;
  }

  // ── Expose ──
  return {
    // State
    leaves,
    currentLeave,
    pendingCount,
    loading,
    meta,

    // Computed
    approvedLeaves,
    pendingLeaves,
    rejectedLeaves,
    cancelledLeaves,
    totalDaysApproved,

    // Actions
    fetchLeaves,
    fetchLeave,
    createLeave,
    updateLeave,
    deleteLeave,
    approveLeave,
    rejectLeave,
    cancelLeave,
    fetchPendingCount,
    decrement,
    reset,
  };
});
