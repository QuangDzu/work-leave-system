<template>
  <div class="max-w-3xl space-y-8">
    <!-- Cấu hình ngày phép -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
      <h3 class="text-xl font-bold text-slate-800 mb-6">
        Cấu hình ngày phép mặc định
      </h3>
      <div class="space-y-6">
        <div
          v-for="type in leaveTypes"
          :key="type.id"
          class="flex items-center justify-between"
        >
          <div>
            <p class="font-semibold text-slate-700">{{ type.name }}</p>
            <p class="text-sm text-slate-500">
              Số ngày phép mặc định cho nhân viên mới
            </p>
          </div>
          <div class="flex items-center gap-4">
            <button
              @click="type.balance--"
              class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-xl font-bold"
            >
              -
            </button>
            <span class="w-16 text-center text-2xl font-bold text-slate-800">{{
              type.balance
            }}</span>
            <button
              @click="type.balance++"
              class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-xl font-bold"
            >
              +
            </button>
            <span class="text-slate-500">ngày</span>
          </div>
        </div>
      </div>
      <button
        @click="saveSettings"
        class="mt-8 px-6 py-3 bg-sky-600 text-white rounded-2xl font-semibold hover:bg-sky-700"
      >
        Lưu cấu hình
      </button>
    </div>

    <!-- Ngày lễ -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-slate-800">
          Quản lý ngày lễ trong năm
        </h3>
        <button
          @click="addHoliday"
          class="px-5 py-2 bg-sky-50 text-sky-700 rounded-2xl font-semibold hover:bg-sky-100"
        >
          + Thêm ngày lễ
        </button>
      </div>

      <div class="space-y-3">
        <div
          v-for="(holiday, i) in holidays"
          :key="i"
          class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl"
        >
          <div>
            <p class="font-medium">{{ holiday.name }}</p>
            <p class="text-sm text-slate-500">{{ holiday.date }}</p>
          </div>
          <button
            @click="holidays.splice(i, 1)"
            class="text-rose-500 hover:text-rose-700"
          >
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Cài đặt hệ thống -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
      <h3 class="text-xl font-bold text-slate-800 mb-6">Cài đặt hệ thống</h3>
      <div class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold">Tự động gửi nhắc nhở</p>
            <p class="text-sm text-slate-500">
              Gửi email khi đơn chờ duyệt quá 3 ngày
            </p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              v-model="systemSettings.autoRemind"
              class="sr-only peer"
            />
            <div
              class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"
            ></div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const leaveTypes = ref([
  { id: 1, name: "Phép năm", balance: 12 },
  { id: 2, name: "Nghỉ ốm", balance: 5 },
  { id: 3, name: "Không lương", balance: 10 },
]);

const holidays = ref([
  { name: "Tết Dương lịch", date: "01/01/2026" },
  { name: "Giải phóng miền Nam", date: "30/04/2026" },
]);

const systemSettings = ref({
  autoRemind: true,
});

const saveSettings = () => {
  alert("Đã lưu cấu hình thành công!");
};

const addHoliday = () => {
  const name = prompt("Tên ngày lễ:");
  const date = prompt("Ngày (DD/MM/YYYY):");
  if (name && date) holidays.value.push({ name, date });
};
</script>
