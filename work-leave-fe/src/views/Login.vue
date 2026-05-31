<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
          <div
            class="inline-flex items-center gap-2 text-sky-600 font-bold text-xl mb-4"
          >
            <div class="p-1.5 bg-sky-100 rounded-lg">
              <Icon name="CalendarCheck" class="w-6 h-6" />
            </div>
            WorkLeave Hub
          </div>
          <h1 class="text-2xl font-bold text-slate-800">Đăng nhập</h1>
          <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý nghỉ phép</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label
              class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5"
              >Email</label
            >
            <input
              v-model="form.email"
              type="email"
              placeholder="you@company.com"
              :class="[
                'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none transition-all text-sm',
                errors.email
                  ? 'border-rose-400 bg-rose-50'
                  : 'border-slate-200',
              ]"
            />
            <p v-if="errors.email" class="text-rose-500 text-xs mt-1">
              {{ errors.email }}
            </p>
          </div>

          <div>
            <label
              class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5"
              >Mật khẩu</label
            >
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              :class="[
                'w-full p-3 rounded-xl border focus:ring-2 focus:ring-sky-500 outline-none transition-all text-sm',
                errors.password
                  ? 'border-rose-400 bg-rose-50'
                  : 'border-slate-200',
              ]"
            />
            <p v-if="errors.password" class="text-rose-500 text-xs mt-1">
              {{ errors.password }}
            </p>
          </div>

          <div
            v-if="serverError"
            class="p-3 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-sm flex items-center gap-2"
          >
            <Icon name="AlertCircle" class="w-4 h-4 shrink-0" />
            {{ serverError }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-sky-600 text-white rounded-xl font-bold hover:bg-sky-700 shadow-lg shadow-sky-200 transition-all disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <i
              v-if="loading"
              data-lucide="loader-2"
              class="w-4 h-4 animate-spin"
            ></i>
            <Icon name="LogIn" class="w-4 h-4" />
            {{ loading ? "Đang đăng nhập..." : "Đăng nhập" }}
          </button>
        </form>

        <!-- Demo quick login -->
        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
          <p class="text-xs text-slate-400 mb-3">
            Tài khoản demo (password:
            <span class="font-bold text-sky-600">password123</span>)
          </p>
          <div class="flex gap-2 justify-center">
            <button
              @click="fillDemo('admin@company.com')"
              class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-1"
            >
              <Icon name="ShieldUser" class="w-3 h-3" /> Admin
            </button>
            <button
              @click="fillDemo('manager@company.com')"
              class="px-3 py-1.5 bg-sky-50 text-sky-700 text-xs font-bold rounded-lg hover:bg-sky-100 transition-colors flex items-center gap-1"
            >
              <Icon name="Briefcase" class="w-3 h-3" /> Manager
            </button>
            <button
              @click="fillDemo('an.nguyen@company.com')"
              class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1"
            >
              <Icon name="User" class="w-3 h-3" /> Employee
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from "@/components/ui/Icon.vue";
import { useAuthStore } from "@/stores/authStore";
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = reactive({ email: "", password: "" });
const errors = reactive({ email: "", password: "" });
const serverError = ref("");
const loading = ref(false);

function fillDemo(email) {
  form.email = email;
  form.password = "password123";
}

async function handleLogin() {
  errors.email = "";
  errors.password = "";
  serverError.value = "";

  if (!form.email) {
    errors.email = "Vui lòng nhập email.";
    return;
  }
  if (!form.password) {
    errors.password = "Vui lòng nhập mật khẩu.";
    return;
  }

  loading.value = true;
  try {
    await auth.login(form.email, form.password);
    router.push(route.query.redirect || "/dashboard");
  } catch (e) {
    serverError.value = e.response?.data?.message || "Đăng nhập thất bại.";
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  if (window.lucide) lucide.createIcons();
});
</script>
