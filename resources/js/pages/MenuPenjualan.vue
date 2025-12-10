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
            <h1 class="text-xl font-semibold">PENJUALAN</h1>
            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/storage/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white"
                />
                <span>{{ nama }}</span>
                <!-- <button
                    @click="goBack"
                    class="bg-white text-[#3674B5] px-3 py-1 rounded hover:bg-gray-200 text-sm transition"
                >
                    Kembali
                </button> -->
            </div>
        </nav>
        <!-- <h1
            class="text-[#6C6565] text-xl font-semibold flex mt-5 items-center gap-3 mx-10"
        >
            Penjualan
        </h1> -->
        <!-- Konten utama -->
        <div class="px-8 mt-6 flex flex-wrap gap-4">
            <FormOutput
                class="w-80 rounded-10px"
                label="Pengguna"
                :value="namapengguna"
            />
            <FormOutput
                class="w-40 rounded-10px"
                label="Tanggal"
                :value="tanggal"
            />
            <FormInput
                class="w-80 rounded-10px"
                label="Pelanggan"
                v-model="namapelanggan"
            />
        </div>
        <div class="px-8 mt-6 flex flex-row gap-4 items-start">
            <!-- Tambah Pembelian di kiri -->
            <FormTambahPembelian
                @tambah-barang="tambahBarangKeDaftar"
                class="w-80"
            />

            <!-- Daftar Pembelian di kanan -->
            <DaftarPembelian
                :daftarPembelian="daftarPembelian"
                class="flex-1 max-w-290"
            />
        </div>
    </div>
</template>

<script>
import FormInput from "../components/FormInput.vue";
import FormOutput from "../components/FormOutput.vue";
import DaftarPembelian from "../components/DaftarPembelian.vue";
import FormTambahPembelian from "../components/FormTambahPembelian.vue";

export default {
    components: { FormInput, FormOutput, DaftarPembelian, FormTambahPembelian },
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
