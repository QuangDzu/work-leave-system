<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[200] flex items-center justify-center p-4"
        @click.self="$emit('update:modelValue', false)"
      >
        <Transition name="modal-pop">
          <div
            v-if="modelValue"
            class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md"
          >
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex items-center gap-4">
              <div
                class="w-11 h-11 rounded-2xl bg-rose-100 flex items-center justify-center shrink-0"
              >
                <Icon name="CircleX" class="w-5 h-5 text-rose-600" />
              </div>
              <div>
                <h3 class="font-bold text-slate-800">Từ chối đơn nghỉ phép</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                  Vui lòng nhập lý do để thông báo cho nhân viên
                </p>
              </div>
              <button
                @click="$emit('update:modelValue', false)"
                class="ml-auto p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors"
              >
                <Icon name="X" class="w-5 h-5" />
              </button>
            </div>

            <!-- Leave info summary -->
            <div
              v-if="leave"
              class="mx-6 mt-5 p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm"
            >
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <p class="text-[10px] text-slate-400 uppercase font-bold">
                    Nhân viên
                  </p>
                  <p class="font-semibold text-slate-700 mt-0.5">
                    {{ leave.user?.name }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 uppercase font-bold">
                    Loại nghỉ
                  </p>
                  <p class="font-semibold text-slate-700 mt-0.5">
                    {{ typeLabel(leave.type) }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 uppercase font-bold">
                    Từ ngày
                  </p>
                  <p class="font-semibold text-slate-700 mt-0.5">
                    {{ fmt(leave.start_date) }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 uppercase font-bold">
                    Số ngày
                  </p>
                  <p class="font-semibold text-slate-700 mt-0.5">
                    {{ leave.total_days }} ngày
                  </p>
                </div>
              </div>
            </div>

            <!-- Form -->
            <div class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">
                  Lý do từ chối <span class="text-rose-500">*</span>
                </label>
                <textarea
                  v-model="reason"
                  rows="4"
                  placeholder="VD: Thời điểm này phòng ban đang có dự án quan trọng, không thể vắng mặt..."
                  :class="[
                    'w-full p-3 rounded-xl border focus:ring-2 focus:ring-rose-400 outline-none resize-none text-sm transition-all',
                    error ? 'border-rose-400 bg-rose-50' : 'border-slate-200',
                  ]"
                  @input="error = ''"
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                  <p v-if="error" class="text-rose-500 text-xs">{{ error }}</p>
                  <p v-else class="text-xs text-slate-400">
                    Tối thiểu 10 ký tự
                  </p>
                  <p
                    :class="[
                      'text-xs',
                      reason.length < 10
                        ? 'text-slate-400'
                        : 'text-emerald-600',
                    ]"
                  >
                    {{ reason.length }}/1000
                  </p>
                </div>
              </div>

              <!-- Warning note -->
              <div
                class="p-3 bg-amber-50 border border-amber-100 rounded-xl flex gap-2.5 text-xs text-amber-700"
              >
                <Icon name="TriangleAlert" class="w-4 h-4 shrink-0 mt-0.5" />
                <span
                  >Lý do sẽ được gửi email thông báo tới nhân viên ngay sau khi
                  xác nhận.</span
                >
              </div>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex gap-3">
              <button
                @click="$emit('update:modelValue', false)"
                class="flex-1 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-colors text-sm"
              >
                Hủy bỏ
              </button>
              <button
                @click="handleConfirm"
                :disabled="loading"
                class="flex-1 py-2.5 bg-rose-600 text-white rounded-xl font-bold hover:bg-rose-700 shadow-lg shadow-rose-200 transition-colors text-sm disabled:opacity-60 flex items-center justify-center gap-2"
              >
                <Icon
                  v-if="loading"
                  name="LoaderCircle"
                  class="w-4 h-4 animate-spin"
                />
                <Icon v-else name="CircleX" class="w-4 h-4" />
                Xác nhận từ chối
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { nextTick, ref, watch } from "vue";
import Icon from "../ui/Icon.vue";

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  leave: { type: Object, default: null },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue", "confirm"]);

const reason = ref("");
const error = ref("");

// Reset khi mở modal
watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      reason.value = "";
      error.value = "";
      nextTick(() => {
        if (window.lucide) lucide.createIcons();
      });
    }
  },
);

function typeLabel(t) {
  return { annual: "Phép năm", sick: "Nghỉ ốm", unpaid: "Không lương" }[t] || t;
}

function fmt(d) {
  if (!d) return "-";
  return new Date(d).toLocaleDateString("vi-VN");
}

function handleConfirm() {
  if (!reason.value.trim()) {
    error.value = "Vui lòng nhập lý do từ chối.";
    return;
  }
  if (reason.value.trim().length < 10) {
    error.value = "Lý do phải có ít nhất 10 ký tự.";
    return;
  }
  emit("confirm", reason.value.trim());
}
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
.modal-pop-enter-active,
.modal-pop-leave-active {
  transition: all 0.2s ease;
}
.modal-pop-enter-from,
.modal-pop-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-8px);
}
</style>
