<template>
  <div class="space-y-6">
    <!-- Toolbar -->
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
    >
      <div class="flex p-1 bg-slate-200/50 rounded-lg">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="switchTab(tab.id)"
          :class="
            activeTab === tab.id
              ? 'bg-white text-slate-900 shadow-sm'
              : 'text-slate-500 hover:text-slate-700'
          "
          class="px-4 py-1.5 text-sm font-medium rounded-md transition-all"
        >
          {{ tab.name }}
        </button>
      </div>
      <div class="flex gap-2">
        <button
          v-if="auth.hasPermission('report.view')"
          @click="exportExcel"
          class="px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-semibold text-sm hover:bg-emerald-100 flex items-center gap-2 transition-colors"
        >
          <Icon name="FileSpreadsheet" class="w-4 h-4" /> Xuất Excel
        </button>
        <button
          v-if="auth.hasPermission('leave.create')"
          @click="openCreateDrawer"
          class="px-5 py-2.5 bg-sky-600 text-white rounded-xl font-semibold text-sm hover:bg-sky-700 shadow-lg shadow-sky-200 flex items-center gap-2 transition-colors"
        >
          <Icon name="Plus" class="w-4 h-4" /> Tạo đơn
        </button>
      </div>
    </div>

    <!-- Table card -->
    <div
      class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
    >
      <!-- Filters -->
      <div
        class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3"
      >
        <span
          class="text-sm font-medium text-slate-500 flex items-center gap-1.5"
        >
          <Icon name="ListFilter" class="w-4 h-4" /> Lọc:
        </span>
        <select
          v-model="filter.type"
          @change="loadLeaves()"
          class="px-3 py-1.5 text-sm bg-slate-100 border-none rounded-lg outline-none focus:ring-2 focus:ring-sky-500"
        >
          <option value="">Tất cả loại</option>
          <option value="annual">Phép năm</option>
          <option value="sick">Nghỉ ốm</option>
          <option value="unpaid">Không lương</option>
        </select>
        <div class="flex items-center gap-2">
          <input
            v-model="filter.from_date"
            type="date"
            @change="loadLeaves()"
            class="px-3 py-1.5 text-sm bg-slate-100 border-none rounded-lg outline-none focus:ring-2 focus:ring-sky-500"
          />
          <span class="text-slate-400 text-xs"
            ><Icon name="MoveRight" class="w-4 h-4"
          /></span>
          <input
            v-model="filter.to_date"
            type="date"
            @change="loadLeaves()"
            class="px-3 py-1.5 text-sm bg-slate-100 border-none rounded-lg outline-none focus:ring-2 focus:ring-sky-500"
          />
        </div>
        <button
          v-if="filter.type || filter.from_date || filter.to_date"
          @click="clearFilter"
          class="px-3 py-1.5 text-xs text-slate-500 hover:text-rose-500 flex items-center gap-1 transition-colors"
        >
          <Icon name="X" class="w-3 h-3" /> Xóa lọc
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center text-slate-400">
        <Icon name="LoaderCircle" class="w-8 h-8 mx-auto animate-spin" />
      </div>

      <!-- Table -->
      <LeaveTable
        v-else
        :leaves="leaves"
        :showUser="auth.hasPermission('user.view')"
        @approve="handleApprove"
        @reject="openRejectModal"
        @edit="openEditDrawer"
        @cancel="handleCancel"
      />

      <!-- Pagination -->
      <div
        v-if="meta.last_page > 1"
        class="p-4 border-t border-slate-100 flex items-center justify-between"
      >
        <p class="text-xs text-slate-500">
          {{ leaves.length }} / {{ meta.total }} đơn
        </p>
        <div class="flex gap-2">
          <button
            :disabled="meta.current_page === 1"
            @click="loadLeaves(meta.current_page - 1)"
            class="p-1.5 border border-slate-200 rounded-md text-slate-400 disabled:opacity-50 hover:bg-slate-50"
          >
            <Icon name="ChevronLeft" class="w-4 h-4" />
          </button>
          <span class="px-3 py-1.5 text-sm text-slate-500"
            >{{ meta.current_page }} / {{ meta.last_page }}</span
          >
          <button
            :disabled="meta.current_page === meta.last_page"
            @click="loadLeaves(meta.current_page + 1)"
            class="p-1.5 border border-slate-200 rounded-md text-slate-400 disabled:opacity-50 hover:bg-slate-50"
          >
            <Icon name="ChevronRight" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- ===== REJECT MODAL ===== -->
    <RejectModal
      v-model="rejectModal.open"
      :leave="rejectModal.leave"
      :loading="rejectModal.loading"
      @confirm="handleRejectConfirm"
    />

    <!-- ===== DRAWER TẠO / SỬA ĐƠN ===== -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="drawerOpen"
          class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100]"
          @click="drawerOpen = false"
        ></div>
      </Transition>
      <Transition name="slide-over">
        <div
          v-if="drawerOpen"
          class="fixed inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl z-[101] flex flex-col"
        >
          <!-- Drawer header -->
          <div
            class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0"
          >
            <h3 class="text-xl font-bold text-slate-800">
              {{ editId ? "Chỉnh sửa đơn nghỉ" : "Tạo đơn nghỉ phép mới" }}
            </h3>
            <button
              @click="drawerOpen = false"
              class="p-2 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50"
            >
              <Icon name="X" class="w-6 h-6" />
            </button>
          </div>

          <!-- Drawer body -->
          <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar">
            <!-- Info -->
            <div
              class="p-4 bg-sky-50 rounded-xl border border-sky-100 flex gap-3"
            >
              <Icon name="Info" class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" />

              <p class="text-xs text-sky-700 leading-relaxed">
                Số dư phép năm:
                <b>{{ auth.user?.remaining_days ?? "—" }} ngày</b>. Vui lòng
                chọn thời gian hợp lý, không trùng đơn đã duyệt.
              </p>
            </div>

            <!-- Type -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Loại nghỉ <span class="text-rose-500">*</span></label
              >
              <select
                v-model="form.type"
                :class="[
                  'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none bg-white',
                  formErrors.type ? 'border-rose-400' : 'border-slate-200',
                ]"
              >
                <option value="">Chọn loại nghỉ</option>
                <option value="annual">Nghỉ phép năm</option>
                <option value="sick">Nghỉ ốm</option>
                <option value="unpaid">Nghỉ không lương</option>
              </select>
              <p v-if="formErrors.type" class="text-rose-500 text-xs mt-1">
                {{ formErrors.type }}
              </p>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5"
                  >Từ ngày <span class="text-rose-500">*</span></label
                >
                <input
                  v-model="form.start_date"
                  type="date"
                  :min="today"
                  :class="[
                    'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none',
                    formErrors.start_date
                      ? 'border-rose-400'
                      : 'border-slate-200',
                  ]"
                />
                <p
                  v-if="formErrors.start_date"
                  class="text-rose-500 text-xs mt-1"
                >
                  {{ formErrors.start_date }}
                </p>
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5"
                  >Đến ngày <span class="text-rose-500">*</span></label
                >
                <input
                  v-model="form.end_date"
                  type="date"
                  :min="form.start_date || today"
                  :class="[
                    'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none',
                    formErrors.end_date
                      ? 'border-rose-400'
                      : 'border-slate-200',
                  ]"
                />
                <p
                  v-if="formErrors.end_date"
                  class="text-rose-500 text-xs mt-1"
                >
                  {{ formErrors.end_date }}
                </p>
              </div>
            </div>

            <!-- Days preview -->
            <div
              v-if="calculatedDays !== null"
              class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between"
            >
              <div class="flex items-center gap-2 text-sm text-slate-600">
                <Icon name="CalendarDays" class="w-4 h-4 text-sky-600" />
                Tổng số ngày làm việc:
              </div>
              <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-sky-600"
                  >{{ calculatedDays }} ngày</span
                >
                <span
                  v-if="
                    form.type === 'annual' &&
                    auth.user?.remaining_days !== undefined
                  "
                  :class="
                    calculatedDays > auth.user.remaining_days
                      ? 'text-rose-500 font-semibold'
                      : 'text-slate-400'
                  "
                  class="text-xs"
                >
                  / còn {{ auth.user.remaining_days }}
                </span>
              </div>
            </div>

            <!-- Reason -->
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Lý do <span class="text-rose-500">*</span></label
              >
              <textarea
                v-model="form.reason"
                rows="4"
                placeholder="Nhập lý do cụ thể..."
                :class="[
                  'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none resize-none',
                  formErrors.reason ? 'border-rose-400' : 'border-slate-200',
                ]"
              >
              </textarea>
              <p v-if="formErrors.reason" class="text-rose-500 text-xs mt-1">
                {{ formErrors.reason }}
              </p>
            </div>
          </div>

          <!-- Drawer footer -->
          <div class="p-6 border-t border-slate-100 flex gap-3 shrink-0">
            <button
              @click="drawerOpen = false"
              class="flex-1 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-colors"
            >
              Hủy bỏ
            </button>
            <button
              @click="handleSubmit"
              :disabled="submitting"
              class="flex-1 py-3 bg-sky-600 text-white rounded-xl font-bold hover:bg-sky-700 shadow-lg shadow-sky-100 transition-colors disabled:opacity-60 flex items-center justify-center gap-2"
            >
              <Icon
                v-if="submitting"
                name="LoaderCircle"
                class="w-4 h-4 animate-spin"
              />
              {{ editId ? "Cập nhật đơn" : "Gửi đơn nghỉ" }}
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import RejectModal from "@/components/common/RejectModal.vue";
import LeaveTable from "@/components/leaves/LeaveTable.vue";
import { useConfirm } from "@/helpers/composables/useConfirm";
import { leaveService } from "@/services/leaveService";
import { useAuthStore } from "@/stores/authStore";
import { useLeaveStore } from "@/stores/leaveStore";
import { computed, nextTick, onMounted, reactive, ref } from "vue";
import { toast } from "vue-sonner";
import Icon from "../components/ui/Icon.vue";

const { confirm } = useConfirm();

const auth = useAuthStore();
const leaveStore = useLeaveStore();

// List state
const leaves = ref([]);
const loading = ref(false);
const meta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});
const filter = reactive({ type: "", from_date: "", to_date: "" });
const activeTab = ref("all");

const tabs = [
  { id: "all", name: "Tất cả" },
  { id: "pending", name: "Chờ duyệt" },
  { id: "approved", name: "Đã duyệt" },
  { id: "rejected", name: "Từ chối" },
];

// ── Reject modal state ──
const rejectModal = reactive({ open: false, leave: null, loading: false });

// Drawer state
const drawerOpen = ref(false);
const editId = ref(null);
const submitting = ref(false);
const form = reactive({ type: "", start_date: "", end_date: "", reason: "" });
const formErrors = reactive({
  type: "",
  start_date: "",
  end_date: "",
  reason: "",
});
const today = new Date().toISOString().split("T")[0];

const HOLIDAYS = ["01-01", "04-30", "05-01", "09-02"];
const calculatedDays = computed(() => {
  if (!form.start_date || !form.end_date) return null;
  const s = new Date(form.start_date),
    e = new Date(form.end_date);
  if (s > e) return null;
  let days = 0,
    cur = new Date(s);
  while (cur <= e) {
    const md =
      String(cur.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(cur.getDate()).padStart(2, "0");
    if (cur.getDay() !== 0 && cur.getDay() !== 6 && !HOLIDAYS.includes(md))
      days++;
    cur.setDate(cur.getDate() + 1);
  }
  return days;
});

// ── Helpers ──
function switchTab(id) {
  activeTab.value = id;
  loadLeaves();
}

function clearFilter() {
  filter.type = "";
  filter.from_date = "";
  filter.to_date = "";
  loadLeaves();
}

// ── Load ──
async function loadLeaves(page = 1) {
  loading.value = true;
  try {
    const params = { page };
    if (activeTab.value !== "all") params.status = activeTab.value;
    if (filter.type) params.type = filter.type;
    if (filter.from_date) params.from_date = filter.from_date;
    if (filter.to_date) params.to_date = filter.to_date;
    const res = await leaveService.list(params);
    leaves.value = res.data ?? [];
    Object.assign(meta, res.meta ?? {});
  } catch {
    toast.error("Không thể tải danh sách đơn!");
  } finally {
    loading.value = false;
    nextTick(() => {
      if (window.lucide) lucide.createIcons();
    });
  }
}

// ── Approve ──
async function handleApprove(id) {
  const ok = await confirm({
    title: "Duyệt đơn nghỉ?",
    message: "Đơn này sẽ được phê duyệt và trừ vào số ngày phép của nhân viên.",
    confirmText: "Duyệt",
    cancelText: "Hủy",
    type: "warning",
  });

  if (!ok) return;

  const toastId = toast.loading("Đang duyệt...");

  try {
    await leaveService.approve(id);
    toast.success("Đã duyệt đơn nghỉ!", { id: toastId });
    loadLeaves(meta.current_page);
    leaveStore.decrement();
    auth.fetchMe();
  } catch (e) {
    toast.error(e.response?.data?.message || "Lỗi khi duyệt!", { id: toastId });
  }
}

// ── Reject — open modal ──
function openRejectModal(leave) {
  rejectModal.leave = leave;
  rejectModal.loading = false;
  rejectModal.open = true;
}

async function handleReject(reason) {
  rejectModal.loading = true;
  const ok = await confirm({
    title: "Từ chối đơn nghỉ?",
    message: "Bạn có chắc muốn từ chối đơn này?",
    confirmText: "Từ chối",
    cancelText: "Hủy",
    type: "danger",
  });

  if (!ok) return;

  const toastId = toast.loading("Đang từ chối...");

  try {
    await leaveService.reject(rejectModal.leave.id, reason);
    rejectModal.open = false;
    toast.success("Đã từ chối đơn nghỉ!", { id: toastId });
    loadLeaves(meta.current_page);
    leaveStore.decrement();
  } catch (e) {
    toast.error(e.response?.data?.message || "Lỗi khi từ chối!", {
      id: toastId,
    });
  } finally {
    rejectModal.loading = false;
  }
}

// ── Cancel ─-
async function handleCancel(id) {
  const ok = await confirm({
    title: "Hủy đơn nghỉ?",
    message: "Bạn có chắc muốn hủy đơn này? Hành động này không thể hoàn tác.",
    confirmText: "Hủy đơn",
    cancelText: "Quay lại",
    type: "danger",
  });

  if (!ok) return;

  const toastId = toast.loading("Đang hủy...");

  try {
    await leaveService.cancel(id);
    toast.success("Đã hủy đơn nghỉ!", { id: toastId });
    loadLeaves(meta.current_page);
    auth.fetchMe();
  } catch (e) {
    toast.error(e.response?.data?.message || "Lỗi khi hủy!", { id: toastId });
  }
}

function exportExcel() {
  toast.info("Tính năng xuất Excel đang phát triển!");
}

onMounted(() => {
  loadLeaves();
  auth.fetchMe();
  if (auth.isManagerOrAdmin) leaveStore.fetchPendingCount();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-over-enter-active,
.slide-over-leave-active {
  transition: transform 0.3s ease;
}

.slide-over-enter-from,
.slide-over-leave-to {
  transform: translateX(100%);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
