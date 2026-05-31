import { authService } from "@/services/authService";
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  const token = ref(localStorage.getItem("access_token") || null);
  const permissions = ref([]);
  const roles = ref([]);

  // ── Auth state ──
  const isAuthenticated = computed(() => !!token.value);

  // ── Role shortcuts ──
  const roleName = computed(() => user.value?.role_name ?? "employee");
  const roleDisplay = computed(() => user.value?.role_display ?? "Nhân viên");

  function hasRole(name) {
    return roles.value.some((r) => r.name === name);
  }
  function hasAnyRole(names) {
    return roles.value.some((r) => names.includes(r.name));
  }

  // ── Permission helpers ──
  function hasPermission(permission) {
    return permissions.value.includes(permission);
  }
  function hasAnyPermission(list) {
    return list.some((p) => permissions.value.includes(p));
  }

  // ── Computed shortcuts ──
  const isAdmin = computed(() => hasRole("admin"));
  const isManager = computed(() => hasRole("manager"));
  const isEmployee = computed(() => hasRole("employee"));
  const isManagerOrAdmin = computed(() => hasAnyRole(["admin", "manager"]));

  const canApproveLeave = computed(() => hasPermission("leave.approve"));
  const canManageUsers = computed(() => hasPermission("user.view"));
  const canViewReports = computed(() => hasPermission("report.view"));
  const canManageSettings = computed(() => hasPermission("setting.manage"));

  // ── Helpers ──
  function _applyUser(userData) {
    user.value = userData;
    roles.value = userData.roles ?? [];
    permissions.value = userData.permissions ?? [];
  }

  // ── Actions ──
  async function login(email, password) {
    const res = await authService.login(email, password);
    token.value = res.data.access_token;
    localStorage.setItem("access_token", token.value);
    _applyUser(res.data.user);
    return res;
  }

  async function logout() {
    try {
      await authService.logout();
    } catch {}
    token.value = null;
    user.value = null;
    roles.value = [];
    permissions.value = [];
    localStorage.removeItem("access_token");
  }

  async function fetchMe() {
    try {
      const res = await authService.me();
      _applyUser(res.data);
      return res.data;
    } catch {
      token.value = null;
      user.value = null;
      roles.value = [];
      permissions.value = [];
      localStorage.removeItem("access_token");
    }
  }

  return {
    // State
    user,
    token,
    roles,
    permissions,
    // Computed
    isAuthenticated,
    roleName,
    roleDisplay,
    isAdmin,
    isManager,
    isEmployee,
    isManagerOrAdmin,
    canApproveLeave,
    canManageUsers,
    canViewReports,
    canManageSettings,
    // Methods
    hasRole,
    hasAnyRole,
    hasPermission,
    hasAnyPermission,
    login,
    logout,
    fetchMe,
  };
});
