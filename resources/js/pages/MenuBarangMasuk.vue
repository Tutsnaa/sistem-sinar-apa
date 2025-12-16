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
            <h1 class="text-xl font-semibold">BARANG MASUK</h1>
            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/storage/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <div class="p-6 bg-gray-100 min-h-screen">
            <TambahBarangMasuk
                :editData="editItem"
                @refresh="refreshBarangMasuk"
                @resetEdit="editItem = null"
            />
            <DaftarBarangMasuk
                :barangMasuk="barangMasuk"
                @edit="onEditBarang"
                @delete="hapusBarang"
            />
        </div>
    </div>
</template>

<script>
import axios from "axios";
import TambahBarangMasuk from "../components/TambahBarangMasuk.vue";
import DaftarBarangMasuk from "../components/DaftarBarangMasuk.vue";

export default {
    components: {
        TambahBarangMasuk,
        DaftarBarangMasuk,
    },
    data() {
        return {
            nama: "",
            foto: "",
            barangMasuk: [],
            editItem: null,
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
        this.getBarangMasuk();
    },
    methods: {
        getBarangMasuk() {
            axios.get("/api/barang-masuk").then((res) => {
                console.log("DATA BARANG MASUK:", res.data.data);
                this.barangMasuk = res.data.data ?? [];
            });
        },

        refreshBarangMasuk(dataBaru) {
            const index = this.barangMasuk.findIndex(
                (item) => item.id === dataBaru.id
            );

            if (index !== -1) {
                // replace item lama, tapi tetap ambil relasi barang lama supaya nama tampil
                this.barangMasuk.splice(index, 1, {
                    ...this.barangMasuk[index],
                    ...dataBaru,
                });
            } else {
                this.barangMasuk.push(dataBaru);
            }
        },

        // refreshData() {
        //     console.log("Refreshing data...");
        //     this.getBarangMasuk();
        // },

        goBack() {
            const role = localStorage.getItem("role");
            if (role === "pemilik_toko") {
                this.$router.push("/beranda-pemilik");
            } else if (role === "karyawan") {
                this.$router.push("/beranda-karyawan");
            } else {
                this.$router.push("/login");
            }
        },

        onEditBarang(item) {
            this.editItem = item;
            console.log("Edit item:", item);
        },

        hapusBarang(item) {
            if (!confirm(`Hapus barang "${item.barang?.nama_barang}"?`)) return;

            axios
                .delete(`/api/barang-masuk/${item.id}`)
                .then(() => {
                    console.log("Barang berhasil dihapus");
                    alert("Barang berhasil dihapus");
                    this.getBarangMasuk();
                })
                .catch((err) => {
                    console.error(err.response?.data);
                    alert("Gagal menghapus, cek console");
                });
        },
    },
};
</script>
