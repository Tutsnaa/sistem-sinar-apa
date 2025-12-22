import { createRouter, createWebHistory } from "vue-router";

import Login from "../pages/Login.vue";
import BerandaPemilik from "../pages/BerandaPemilik.vue";
import BerandaKaryawan from "../pages/BerandaKaryawan.vue";
import MenuPenjualan from "../pages/MenuPenjualan.vue";
import MenuBarangMasuk from "../pages/MenuBarangMasuk.vue";
import MenuBarangMasukPemilik from "../pages/MenuBarangMasukPemilik.vue";
import MenuBarang from "../pages/MenuBarang.vue";
import MenuKategori from "../pages/MenuKategori.vue";
import MenuRiwayat from "../pages/MenuRiwayat.vue";
import MenuLaporan from "../pages/MenuLaporan.vue";
import MenuKelolaKaryawan from "../pages/MenuKelolaKaryawan.vue";
import MenuProfil from "../pages/MenuProfil.vue";

const routes = [
    { path: "/", redirect: "/login" },

    {
        path: "/login",
        name: "login",
        component: Login,
    },

    // ===== BERANDA =====
    {
        path: "/beranda-pemilik",
        component: BerandaPemilik,
        meta: { role: "pemilik_toko" },
    },
    {
        path: "/beranda-karyawan",
        component: BerandaKaryawan,
        meta: { role: "karyawan" },
    },

    // ===== MENU BERSAMA =====
    {
        path: "/menu-penjualan",
        component: MenuPenjualan,
        meta: { role: ["pemilik_toko", "karyawan"] },
    },
    {
        path: "/menu-barang",
        component: MenuBarang,
        meta: { role: ["pemilik_toko", "karyawan"] },
    },
    {
        path: "/menu-riwayat",
        component: MenuRiwayat,
        meta: { role: ["pemilik_toko", "karyawan"] },
    },
    {
        path: "/menu-profil",
        component: MenuProfil,
        meta: { role: ["pemilik_toko", "karyawan"] },
    },

    // ===== MENU KHUSUS KARYAWAN =====
    {
        path: "/menu-barang-masuk",
        component: MenuBarangMasuk,
        meta: { role: "karyawan" },
    },

    // ===== MENU KHUSUS PEMILIK =====
    {
        path: "/menu-barang-masuk-pemilik",
        component: MenuBarangMasukPemilik,
        meta: { role: "pemilik_toko" },
    },
    {
        path: "/menu-kategori",
        component: MenuKategori,
        meta: { role: "pemilik_toko" },
    },
    {
        path: "/menu-laporan",
        component: MenuLaporan,
        meta: { role: "pemilik_toko" },
    },
    {
        path: "/menu-kelola-karyawan",
        component: MenuKelolaKaryawan,
        meta: { role: "pemilik_toko" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

/* =========================
   🔐 ROUTE GUARD (WAJIB)
========================= */
router.beforeEach((to, from, next) => {
    const role = localStorage.getItem("role");

    // Belum login
    if (to.path !== "/login" && !role) {
        return next("/login");
    }

    // Role-based access
    if (to.meta.role) {
        // Jika meta role array
        if (Array.isArray(to.meta.role)) {
            if (!to.meta.role.includes(role)) {
                return next(
                    role === "pemilik_toko"
                        ? "/beranda-pemilik"
                        : "/beranda-karyawan"
                );
            }
        }
        // Jika meta role string
        else if (to.meta.role !== role) {
            return next(
                role === "pemilik_toko"
                    ? "/beranda-pemilik"
                    : "/beranda-karyawan"
            );
        }
    }

    next();
});

export default router;
