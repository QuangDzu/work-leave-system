<template>
  <div class="space-y-6">
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
    >
      <p class="text-sm text-slate-500">{{ activeUsers.length }} nhân viên</p>
      <button
        @click="openDrawer(null)"
        class="px-5 py-2.5 bg-slate-900 text-white rounded-xl font-semibold text-sm hover:bg-slate-800 flex items-center gap-2 transition-colors"
      >
        <Icon name="UserPlus" class="w-4 h-4" /> Thêm nhân viên
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <i data-lucide="loader-2" class="w-8 h-8 text-slate-400 animate-spin"></i>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="u in activeUsers"
        :key="u.id"
        class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-sky-300 transition-colors relative group"
      >
        <!-- Action buttons -->
        <div
          class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <button
            @click="openDrawer(u)"
            class="p-1.5 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors"
            title="Sửa"
          >
            <i data-lucide="pencil" class="w-4 h-4"></i>
          </button>
          <button
            @click="handleResetPassword(u)"
            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
            title="Reset mật khẩu"
          >
            <i data-lucide="key" class="w-4 h-4"></i>
          </button>
          <button
            @click="handleDelete(u)"
            class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
            title="Xóa"
          >
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>

        <div class="flex items-center gap-4 mb-6">
          <div
            class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center text-sky-700 text-xl font-bold shrink-0"
          >
            {{ initials(u.name) }}
          </div>
          <div>
            <h4 class="font-bold text-slate-900">{{ u.name }}</h4>
            <p class="text-sm text-slate-500">{{ u.email }}</p>
            <div class="flex gap-2 mt-2">
              <span
                :class="roleBadge(u.role_name)"
                class="px-2 py-0.5 text-[10px] font-bold rounded uppercase"
                >{{ u.role_display }}</span
              >
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-50">
          <div>
            <p class="text-[10px] text-slate-400 uppercase font-bold">
              Phép còn lại
            </p>
            <p class="text-sm font-bold text-slate-700 mt-0.5">
              {{ u.remaining_days }} ngày
            </p>
          </div>
          <div>
            <p class="text-[10px] text-slate-400 uppercase font-bold">
              Loại tài khoản
            </p>
            <p class="text-sm font-bold text-slate-700 mt-0.5 capitalize">
              {{ u.role_display }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Drawer -->
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
          <div
            class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0"
          >
            <h3 class="text-xl font-bold text-slate-800">
              {{ editId ? "Chỉnh sửa nhân viên" : "Thêm nhân viên mới" }}
            </h3>
            <button
              @click="drawerOpen = false"
              class="p-2 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-rose-50"
            >
              <i data-lucide="x" class="w-6 h-6"></i>
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Họ và tên <span class="text-rose-500">*</span></label
              >
              <input
                v-model="form.name"
                type="text"
                placeholder="Nguyễn Văn A"
                class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 outline-none"
              />
              <p v-if="formErrors.name" class="text-rose-500 text-xs mt-1">
                {{ formErrors.name }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Email <span class="text-rose-500">*</span></label
              >
              <input
                v-model="form.email"
                type="email"
                placeholder="example@company.com"
                class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 outline-none"
              />
              <p v-if="formErrors.email" class="text-rose-500 text-xs mt-1">
                {{ formErrors.email }}
              </p>
            </div>
            <div v-if="!editId">
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Mật khẩu <span class="text-rose-500">*</span></label
              >
              <input
                v-model="form.password"
                type="password"
                placeholder="Tối thiểu 8 ký tự"
                class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 outline-none"
              />
              <p v-if="formErrors.password" class="text-rose-500 text-xs mt-1">
                {{ formErrors.password }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Vai trò</label
              >
              <select
                v-model="form.role_name"
                class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 outline-none bg-white"
              >
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="employee">Employee</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5"
                >Số ngày phép năm</label
              >
              <input
                v-model.number="form.remaining_days"
                type="number"
                min="0"
                class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 outline-none"
              />
            </div>
          </div>

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
              <i
                v-if="submitting"
                data-lucide="loader-2"
                class="w-4 h-4 animate-spin"
              ></i>
              {{ editId ? "Cập nhật" : "Tạo tài khoản" }}
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { useConfirm } from "@/helpers/composables/useConfirm";
import { userService } from "@/services/userService";
import { computed, nextTick, onMounted, reactive, ref } from "vue";
import { toast } from "vue-sonner";
import Icon from "../components/ui/Icon.vue";

const { confirm } = useConfirm();

const users = ref([]);
const loading = ref(false);
const submitting = ref(false);
const drawerOpen = ref(false);
const editId = ref(null);

const form = reactive({
  name: "",
  email: "",
  password: "",
  type: 2,
  remaining_days: 12,
});
const formErrors = reactive({ name: "", email: "", password: "" });

const activeUsers = computed(() => users.value);

function initials(name) {
  if (!name) return "?";
  return name
    .split(" ")
    .map((w) => w[0])
    .slice(-2)
    .join("")
    .toUpperCase();
}

function roleBadge(roleName) {
  return (
    {
      admin: "bg-indigo-50 text-indigo-700",
      manager: "bg-sky-50 text-sky-700",
      employee: "bg-slate-100 text-slate-600",
    }[roleName] ?? "bg-slate-100 text-slate-600"
  );
}

function openDrawer(user) {
  formErrors.name = formErrors.email = formErrors.password = "";
  if (user) {
    editId.value = user.id;
    Object.assign(form, {
      name: user.name,
      email: user.email,
      password: "",
      role_name: user.role_name,
      remaining_days: user.remaining_days,
    });
  } else {
    editId.value = null;
    Object.assign(form, {
      name: "",
      email: "",
      password: "",
      type: 2,
      remaining_days: 12,
    });
  }
  drawerOpen.value = true;
  nextTick(() => {
    if (window.lucide) lucide.createIcons();
  });
}

function validate() {
  let ok = true;
  formErrors.name = formErrors.email = formErrors.password = "";
  if (!form.name.trim()) {
    formErrors.name = "Vui lòng nhập họ tên.";
    ok = false;
  }
  if (!form.email.trim()) {
    formErrors.email = "Vui lòng nhập email.";
    ok = false;
  }
  if (!editId.value && !form.password) {
    formErrors.password = "Vui lòng nhập mật khẩu.";
    ok = false;
  }
  return ok;
}

async function handleSubmit() {
  if (!validate()) return;
  submitting.value = true;

  const toastId = toast.loading(
    editId.value ? "Đang cập nhật..." : "Đang tạo tài khoản...",
  );

  try {
    if (editId.value) {
      const payload = {
        name: form.name,
        email: form.email,
        role_name: form.role_name,
        remaining_days: form.remaining_days,
      };
      await userService.update(editId.value, payload);
      toast.success("Đã cập nhật nhân viên!", { id: toastId });
    } else {
      await userService.create({ ...form });
      toast.success("Đã thêm nhân viên mới!", { id: toastId });
    }
    drawerOpen.value = false;
    loadUsers();
  } catch (e) {
    const errs = e.response?.data?.errors || {};
    Object.entries(errs).forEach(([k, v]) => {
      if (formErrors[k] !== undefined)
        formErrors[k] = Array.isArray(v) ? v[0] : v;
    });
    toast.error(e.response?.data?.message || "Có lỗi xảy ra!", { id: toastId });
  } finally {
    submitting.value = false;
  }
}

async function handleDelete(user) {
  const ok = await confirm({
    title: `Xóa ${user.name}?`,
    message:
      "Nhân viên này sẽ bị xóa khỏi hệ thống. Hành động này không thể hoàn tác.",
    confirmText: "Xóa",
    cancelText: "Hủy",
    type: "danger",
  });

  if (!ok) return;

  const toastId = toast.loading("Đang xóa...");

  try {
    await userService.delete(user.id);
    toast.success(`Đã xóa ${user.name}!`, { id: toastId });
    loadUsers();
  } catch {
    toast.error("Không thể xóa!", { id: toastId });
  }
}

function handleResetPassword(user) {
  toast.success(`Đã gửi email reset mật khẩu cho ${user.name}!`);
}

async function loadUsers() {
  loading.value = true;
  try {
    const res = await userService.list();
    users.value = res.data ?? [];
  } catch {
    toast.error("Không thể tải danh sách nhân viên!");
  } finally {
    loading.value = false;
    nextTick(() => {
      if (window.lucide) lucide.createIcons();
    });
  }
}

onMounted(() => {
  loadUsers();
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
</style>
