import { createRouter, createWebHistory } from "vue-router";

import Login from "../pages/Login.vue";
import BerandaPemilik from "../pages/BerandaPemilik.vue";
import BerandaKaryawan from "../pages/BerandaKaryawan.vue";
import MenuPenjualan from "../pages/MenuPenjualan.vue";
import MenuBarangMasuk from "../pages/MenuBarangMasuk.vue";
import MenuBarang from "../pages/MenuBarang.vue";
import MenuKategori from "../pages/MenuKategori.vue";
import MenuRiwayat from "../pages/MenuRiwayat.vue";
import MenuLaporan from "../pages/MenuLaporan.vue";
import MenuKelolaKaryawan from "../pages/MenuKelolaKaryawan.vue";
import MenuProfil from "../pages/MenuProfil.vue";

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
    {
        path: "/menu-penjualan",
        name: "menupenjualan",
        component: MenuPenjualan,
    },
    {
        path: "/menu-barang-masuk",
        name: "menubarangmasuk",
        component: MenuBarangMasuk,
    },
    {
        path: "/menu-barang",
        name: "menubarang",
        component: MenuBarang,
    },
    {
        path: "/menu-kategori",
        name: "menukategori",
        component: MenuKategori,
    },
    {
        path: "/menu-riwayat",
        name: "menuriwayat",
        component: MenuRiwayat,
    },
    {
        path: "/menu-laporan",
        name: "menulaporan",
        component: MenuLaporan,
    },
    {
        path: "/menu-kelola-karyawan",
        name: "menukelolakaryawan",
        component: MenuKelolaKaryawan,
    },
    {
        path: "/menu-profil",
        name: "menuprofil",
        component: MenuProfil,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
