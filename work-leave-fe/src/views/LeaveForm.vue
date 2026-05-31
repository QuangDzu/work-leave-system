<template>
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <!-- Info box -->
      <div
        class="p-4 bg-sky-50 rounded-xl border border-sky-100 flex gap-3 mb-6"
      >
        <Icon name="Info" class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" />
        <p class="text-xs text-sky-700 leading-relaxed">
          Số dư nghỉ phép năm hiện tại:
          <b>{{ auth.user?.remaining_days ?? "—" }} ngày</b>. Vui lòng chọn thời
          gian hợp lý, không trùng đơn đã duyệt.
        </p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-5">
        <!-- Type -->
        <div>
          <label class="block text-sm font-bold text-slate-700 mb-1.5">
            Loại nghỉ <span class="text-rose-500">*</span>
          </label>
          <select
            v-model="form.type"
            :class="[
              'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none bg-white',
              errors.type ? 'border-rose-400' : 'border-slate-200',
            ]"
          >
            <option value="">Chọn loại nghỉ</option>
            <option value="annual">Nghỉ phép năm</option>
            <option value="sick">Nghỉ ốm</option>
            <option value="unpaid">Nghỉ không lương</option>
          </select>
          <p v-if="errors.type" class="text-rose-500 text-xs mt-1">
            {{ errors.type }}
          </p>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">
              Từ ngày <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.start_date"
              type="date"
              :min="today"
              :class="[
                'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none',
                errors.start_date ? 'border-rose-400' : 'border-slate-200',
              ]"
            />
            <p v-if="errors.start_date" class="text-rose-500 text-xs mt-1">
              {{ errors.start_date }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">
              Đến ngày <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.end_date"
              type="date"
              :min="form.start_date || today"
              :class="[
                'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none',
                errors.end_date ? 'border-rose-400' : 'border-slate-200',
              ]"
            />
            <p v-if="errors.end_date" class="text-rose-500 text-xs mt-1">
              {{ errors.end_date }}
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
            Tổng số ngày nghỉ:
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
          <label class="block text-sm font-bold text-slate-700 mb-1.5">
            Lý do nghỉ <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="form.reason"
            rows="4"
            placeholder="Nhập lý do cụ thể..."
            :class="[
              'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none resize-none',
              errors.reason ? 'border-rose-400' : 'border-slate-200',
            ]"
          >
          </textarea>
          <p v-if="errors.reason" class="text-rose-500 text-xs mt-1">
            {{ errors.reason }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-2">
          <button
            type="button"
            @click="$router.push('/leaves')"
            class="flex-1 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-colors"
          >
            Hủy bỏ
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 py-3 bg-sky-600 text-white rounded-xl font-bold hover:bg-sky-700 shadow-lg shadow-sky-100 transition-colors disabled:opacity-60 flex items-center justify-center gap-2"
          >
            <i
              v-if="loading"
              data-lucide="loader-2"
              class="w-4 h-4 animate-spin"
            ></i>
            {{ isEdit ? "Cập nhật đơn" : "Gửi đơn nghỉ" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { leaveService } from "@/services/leaveService";
import { useAuthStore } from "@/stores/authStore";
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { toast } from "vue-sonner";
import Icon from "../components/ui/Icon.vue";

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const isEdit = computed(() => !!route.params.id);
const loading = ref(false);
const today = new Date().toISOString().split("T")[0];

const form = reactive({ type: "", start_date: "", end_date: "", reason: "" });
const errors = reactive({ type: "", start_date: "", end_date: "", reason: "" });

const HOLIDAYS = ["01-01", "04-30", "05-01", "09-02"];
const calculatedDays = computed(() => {
  if (!form.start_date || !form.end_date) return null;
  const s = new Date(form.start_date);
  const e = new Date(form.end_date);
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

function validate() {
  let ok = true;
  errors.type = errors.start_date = errors.end_date = errors.reason = "";
  if (!form.type) {
    errors.type = "Vui lòng chọn loại nghỉ.";
    ok = false;
  }
  if (!form.start_date) {
    errors.start_date = "Vui lòng chọn ngày bắt đầu.";
    ok = false;
  }
  if (!form.end_date) {
    errors.end_date = "Vui lòng chọn ngày kết thúc.";
    ok = false;
  }
  if (!form.reason.trim()) {
    errors.reason = "Vui lòng nhập lý do nghỉ.";
    ok = false;
  }
  return ok;
}

async function handleSubmit() {
  if (!validate()) return;
  loading.value = true;

  const toastId = toast.loading(
    isEdit.value ? "Đang cập nhật..." : "Đang gửi đơn...",
  );

  try {
    if (isEdit.value) {
      await leaveService.update(route.params.id, { ...form });
      toast.success("Đã cập nhật đơn nghỉ!", { id: toastId });
    } else {
      await leaveService.create({ ...form });
      toast.success("Đơn nghỉ đã được gửi!", { id: toastId });
    }
    setTimeout(() => router.push("/leaves"), 1200);
  } catch (e) {
    const errs = e.response?.data?.errors || {};
    Object.entries(errs).forEach(([k, v]) => {
      if (errors[k] !== undefined) errors[k] = Array.isArray(v) ? v[0] : v;
    });
    toast.error(e.response?.data?.message || "Gửi đơn thất bại!", {
      id: toastId,
    });
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  auth.fetchMe();
  if (isEdit.value) {
    try {
      const res = await leaveService.get(route.params.id);
      Object.assign(form, {
        type: res.data.type,
        start_date: res.data.start_date,
        end_date: res.data.end_date,
        reason: res.data.reason,
      });
    } catch {
      toast.error("Không thể tải đơn nghỉ!");
    }
  }
  if (window.lucide) lucide.createIcons();
});
</script>
