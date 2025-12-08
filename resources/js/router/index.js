import { createRouter, createWebHistory } from "vue-router";

import Login from "../pages/Login.vue";
import DashboardPemilik from "../pages/DashboardPemilik.vue";
import DashboardKaryawan from "../pages/DashboardKaryawan.vue";

const routes = [
    { path: "/", redirect: "/login" }, // ⬅ tambahkan ini
    { path: "/login", name: "login", component: Login },
    {
        path: "/dashboard-pemilik",
        name: "dashboardPemilik",
        component: DashboardPemilik,
    },
    {
        path: "/dashboard-karyawan",
        name: "dashboardKaryawan",
        component: DashboardKaryawan,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
