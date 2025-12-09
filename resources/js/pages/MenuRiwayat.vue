<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-4 flex justify-between items-center shadow"
        >
            <h1 class="text-xl font-semibold">Riwayat</h1>
            <div class="flex items-center gap-4">
                <span>{{ nama }}</span>
                <button
                    @click="goBack"
                    class="bg-white text-[#3674B5] px-3 py-1 rounded hover:bg-gray-200 text-sm transition"
                >
                    Kembali
                </button>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    data() {
        return {
            nama: "",
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
    },
    methods: {
        goBack() {
            const role = localStorage.getItem("role");

            if (role === "pemilik_toko") {
                this.$router.push("/beranda-pemilik");
            } else if (role === "karyawan") {
                this.$router.push("/beranda-karyawan");
            } else {
                // jika role tidak ditemukan (misal session hilang)
                this.$router.push("/login");
            }
        },
    },
};
</script>
