<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-4 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">Dashboard Karyawan</h1>
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

        <!-- Konten -->
        <div class="p-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Menu Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md cursor-pointer transition"
                >
                    📦 Kelola Persediaan
                </div>
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md cursor-pointer transition"
                >
                    🛒 Entri Penjualan
                </div>
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md cursor-pointer transition"
                >
                    📑 Laporan Harian
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
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
