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
            <DaftarBarangMasuk
                :barangMasuk="barangMasuk"
                :role="role"
                @update-status="updateStatusBarang"
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
            role: "", // <-- ini penting
            barangMasuk: [],
            editItem: null,
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");
        console.log(role);

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.foto = foto;
        this.role = role;
        this.getBarangMasuk();
    },
    methods: {
        refreshBarangMasuk(updatedItem) {
            if (!updatedItem) {
                // fetch ulang semua data
                this.getBarangMasuk();
            } else {
                // update item lama di array
                const index = this.barangMasuk.findIndex(
                    (item) => item.id === updatedItem.id
                );
                if (index !== -1) {
                    this.barangMasuk.splice(index, 1, updatedItem); // ganti item lama dengan yang baru
                } else {
                    // kalau item baru, push
                    this.barangMasuk.push(updatedItem);
                }
            }
        },
        onEditBarang(item) {
            this.editItem = item;
        },
        hapusBarang(item) {
            // method hapus
        },
        updateStatusBarang({ item, status }) {
            axios
                .put(`/api/barang-masuk/${item.id}`, { ...item, status })
                .then((res) => {
                    // update data lokal agar tabel langsung berubah
                    const index = this.barangMasuk.findIndex(
                        (b) => b.id === item.id
                    );
                    if (index !== -1) {
                        this.barangMasuk.splice(index, 1, res.data.data);
                    }
                    alert(
                        `Status barang "${item.barang?.nama_barang}" berhasil diubah menjadi ${status}`
                    );
                })
                .catch((err) => {
                    console.error(err);
                    alert("Gagal mengubah status, cek console");
                });
        },
        getBarangMasuk() {
            axios.get("/api/barang-masuk").then((res) => {
                console.log("DATA BARANG MASUK:", res.data.data);
                this.barangMasuk = res.data.data ?? [];
            });
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
    },
    //Atur urutan barang
    watch: {
        barangMasuk: {
            deep: true,
            handler() {
                const priority = {
                    Ditolak: 0,
                    Menunggu: 1,
                    Diterima: 2,
                };

                this.barangMasuk.sort((a, b) => {
                    if (priority[a.status] !== priority[b.status]) {
                        return priority[a.status] - priority[b.status];
                    }
                    return new Date(b.created_at) - new Date(a.created_at);
                });
            },
        },
    },
};
</script>
