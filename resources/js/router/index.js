import { createRouter, createWebHistory } from "vue-router";

import Login from "../pages/Login.vue";
import BerandaPemilik from "../pages/BerandaPemilik.vue";
import BerandaKaryawan from "../pages/BerandaKaryawan.vue";

const routes = [
    { path: "/", redirect: "/login" }, // ⬅ tambahkan ini
    { path: "/login", name: "login", component: Login },
    {
        path: "/beranda-pemilik",
        name: "berandaPemilik",
        component: BerandaPemilik,
    },
    {
        path: "/beranda-karyawan",
        name: "berandadKaryawan",
        component: BerandaKaryawan,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
