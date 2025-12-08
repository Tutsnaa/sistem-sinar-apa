<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-4 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">Dashboard Pemilik Toko</h1>
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
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Ringkasan Bisnis
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md transition"
                >
                    💰 Total Penjualan
                    <p class="text-2xl font-bold mt-2">Rp 0</p>
                </div>
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md transition"
                >
                    📦 Stok Menipis
                    <p class="text-2xl font-bold mt-2">0 Barang</p>
                </div>
                <div
                    class="bg-white p-6 rounded shadow hover:shadow-md transition"
                >
                    👥 Karyawan Aktif
                    <p class="text-2xl font-bold mt-2">0 Orang</p>
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
