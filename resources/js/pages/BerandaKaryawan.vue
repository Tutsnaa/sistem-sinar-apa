<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">TOKO SINAR APA</h1>
            <div class="flex items-center gap-4">
                <img
                    :src="`/foto_pengguna/${foto}?v=${Date.now()}`"
                    class="w-10 h-10 rounded-full"
                />

                <span>{{ nama }}</span>
                <button
                    @click="logout"
                    class="bg-white text-[#3674B5] px-4 py-1 rounded hover:bg-gray-200 text-l transition"
                >
                    Keluar
                </button>
            </div>
        </nav>

        <div
            class="flex flex-wrap justify-center gap-4 max-w-6xl mx-auto mt-5 px-4"
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
                            archive
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
                            inventory_2
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
            <!-- TOAST SUCCESS -->
            <div
                v-if="toast"
                class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-2xl animate-slide-down"
            >
                <!-- ICON CHECK -->
                <div class="bg-white/20 rounded-full p-1">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                <!-- NOTIFICATION -->
                <span class="font-semibold text-sm">
                    {{ toast }}
                </span>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.animate-slide-down {
    animation: slideDown 0.4s ease-out;
}
</style>

<script>
import MenuBeranda from "../components/MenuBeranda.vue";

export default {
    components: { MenuBeranda },

    data() {
        return {
            nama: "",
            foto: "",
            toast: "",
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        //TOAST
        const pesan = sessionStorage.getItem("toast_success");
        if (pesan) {
            this.toast = pesan;
            sessionStorage.removeItem("toast_success");

            setTimeout(() => {
                this.toast = "";
            }, 3000);
        }

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
