<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">SISTEM SINAR APA</h1>
            <div class="flex items-center gap-4">
                <img
                    :src="`/foto_pengguna/${foto}?v=${Date.now()}`"
                    class="w-10 h-10 rounded-full"
                />

                <span>{{ nama }}</span>
                <button
                    @click="logout"
                    class="bg-white text-[#3674B5] px-3 py-1 rounded hover:bg-gray-200 text-sm transition"
                >
                    Keluar
                </button>
            </div>
        </nav>

        <div
            class="flex flex-wrap justify-center gap-4 max-w-6xl mx-auto mt-3 px-4"
        >
            <div
                @click="$router.push('/menu-penjualan')"
                class="w-full sm:w-[48%] lg:w-[32%] p-4 rounded box-border"
            >
                <MenuBeranda title="Penjualan" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            shopping_cart
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div
                @click="$router.push('/menu-barang-masuk')"
                class="w-full sm:w-[48%] lg:w-[32%] p-4 rounded box-border"
            >
                <MenuBeranda title="Barang Masuk" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            inventory_2
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div
                @click="$router.push('/menu-barang')"
                class="w-full sm:w-[48%] lg:w-[32%] p-4 rounded box-border"
            >
                <MenuBeranda title="Barang" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            view_list
                        </span>
                    </template>
                </MenuBeranda>
            </div>

            <div
                @click="$router.push('/menu-profil')"
                class="w-full sm:w-[48%] lg:w-[32%] p-4 rounded box-border"
            >
                <MenuBeranda title="Profil" fontsize="text-3xl">
                    <template #icon>
                        <span class="material-icons" style="font-size: 60px">
                            account_circle
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
            nama: "",
            foto: "",
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.foto = foto;
    },

    methods: {
        logout() {
            localStorage.clear(); // opsional, reset auth di frontend
            this.$router.push("/login");
        },
    },
};
</script>
