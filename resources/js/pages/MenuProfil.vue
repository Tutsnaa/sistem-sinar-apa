<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow"
        >
            <h1
                @click="goBack"
                class="flex items-center gap-3 text-xl font-semibold hover:text-gray-200 cursor-pointer"
            >
                <span class="material-icons" style="font-size: 25px"
                    >arrow_back</span
                >Kembali
            </h1>
            <h1 class="text-xl font-semibold">PROFIL</h1>
            <!-- <div class="flex items-center gap-4">
                <span>{{ nama }}</span>
                <button
                    @click="goBack"
                    class="bg-white text-[#3674B5] px-3 py-1 rounded hover:bg-gray-200 text-sm transition"
                >
                    Kembali
                </button>
            </div> -->
        </nav>
        <!-- <h1
            class="text-[#6C6565] text-xl font-semibold flex mt-5 items-center gap-3 mx-10 cursor-pointer hover:text-[#3674B5]"
        >
            <span class="material-icons" style="font-size: 25px"
                >arrow_back</span
            >Kembali
        </h1> -->
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
