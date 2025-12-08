<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-4 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">Beranda Pemilik Toko</h1>
            <div class="flex items-center gap-4">
                <span>Halo, {{ nama }} 👋</span>
                <button
                    @click="logout"
                    class="bg-white text-[#3674B5] px-3 py-1 rounded hover:bg-gray-200 text-sm transition"
                >
                    Logout
                </button>
            </div>
        </nav>

        <div class="flex flex-wrap justify-center mx-60 mt-8">
            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Penjualan" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            shopping_cart
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Barang Masuk" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            inventory_2
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Barang" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            view_list
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Kategori" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            category
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Riwayat" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            history
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Laporan" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            bar_chart
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div class="basis-1/3 flex justify-center mb-8">
                <MenuBeranda title="Kelola Karyawan" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            people
                        </span>
                    </template>
                </MenuBeranda>
            </div>
        </div>
    </div>
</template>

<script>
import MenuBeranda from "../components/MenuBeranda.vue";

export default {
    components: { MenuBeranda },
    data() {
        return {
            nama: this.$nama,
        };
    },
    methods: {
        logout() {
            window.location.href = "/logout";
        },
    },
};
</script>
