import { useAuthStore } from "@/stores/authStore";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/login",
    name: "Login",
    component: () => import("@/views/Login.vue"),
    meta: { guest: true },
  },
  {
    path: "/",
    component: () => import("@/layouts/AppLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      { path: "", redirect: "/dashboard" },
      {
        path: "dashboard",
        name: "Dashboard",
        component: () => import("@/views/Dashboard.vue"),
      },
      {
        path: "leaves",
        name: "Leaves",
        component: () => import("@/views/Leaves.vue"),
        meta: { permission: "leave.view" },
      },
      {
        path: "leaves/create",
        name: "LeaveCreate",
        component: () => import("@/views/LeaveForm.vue"),
        meta: { permission: "leave.create" },
      },
      {
        path: "leaves/:id/edit",
        name: "LeaveEdit",
        component: () => import("@/views/LeaveForm.vue"),
        meta: { permission: "leave.edit" },
      },
      {
        path: "users",
        name: "Users",
        component: () => import("@/views/Users.vue"),
        meta: { permission: "user.view" },
      },
      {
        path: "reports",
        name: "Reports",
        component: () => import("@/views/Reports.vue"),
        meta: { permission: "report.view" },
      },
      {
        path: "settings",
        name: "Settings",
        component: () => import("@/views/Settings.vue"),
        meta: { permission: "setting.manage" },
      },
    ],
  },
  { path: "/:pathMatch(.*)*", redirect: "/dashboard" },
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  // ── Guest routes ──────────────────────────────────────
  if (to.meta.guest) {
    return auth.isAuthenticated ? next({ name: "Dashboard" }) : next();
  }

  // ── Auth required ─────────────────────────────────────
  if (to.meta.requiresAuth || to.meta.permission) {
    if (!auth.isAuthenticated) {
      return next({ name: "Login", query: { redirect: to.fullPath } });
    }

    // Fetch user if not yet loaded
    if (!auth.user) {
      await auth.fetchMe();
      if (!auth.user) return next({ name: "Login" });
    }

    // Permission guard
    if (to.meta.permission && !auth.hasPermission(to.meta.permission)) {
      return next({ name: "Dashboard" });
    }
  }

  next();
});

export default router;
