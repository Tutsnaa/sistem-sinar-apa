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
                >
                Kembali
            </h1>

            <h1 class="text-xl font-semibold">PENJUALAN</h1>

            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/storage/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- Konten Utama -->
        <div class="px-8 mt-6 flex flex-wrap gap-4">
            <FormOutput
                class="w-80 rounded-10px"
                label="Pengguna"
                :value="nama"
            />

            <FormOutput
                class="w-40 rounded-10px"
                label="Tanggal"
                :value="tanggal"
            />

            <FormInput
                id="namaPelanggan"
                class="w-80 rounded-10px"
                label="Pelanggan"
                v-model="namapelanggan"
            />
        </div>

        <div class="px-8 mt-6 flex flex-row gap-4 items-start">
            <!-- Tambah Pembelian -->
            <FormTambahPembelian
                :barang="barang"
                @tambah-barang="tambahBarangKeDaftar"
                class="w-80"
            />

            <!-- Daftar Pembelian -->
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
    props: {
        barang: {
            type: Array,
            default: () => [], // ← supaya tidak undefined
        },
    },

    components: {
        FormInput,
        FormOutput,
        DaftarPembelian,
        FormTambahPembelian,
    },

    data() {
        return {
            nama: "",
            foto: "",
            tanggal: "",
            namapelanggan: "",
            daftarPembelian: [],
        };
    },

    mounted() {
        console.log("DATA BARANG MASUK dari Inertia:", this.barang);

        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        const t = new Date();
        const hari = String(t.getDate()).padStart(2, "0");
        const bulan = String(t.getMonth() + 1).padStart(2, "0");
        const tahun = t.getFullYear();
        this.tanggal = `${hari}-${bulan}-${tahun}`;

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

            if (role === "pemilik_toko") this.$router.push("/beranda-pemilik");
            else if (role === "karyawan")
                this.$router.push("/beranda-karyawan");
            else this.$router.push("/login");
        },

        tambahBarangKeDaftar(barang) {
            this.daftarPembelian.push(barang);
        },
    },
};
</script>
