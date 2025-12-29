<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow fixed top-0 left-0 w-full z-50"
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
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>
        <!-- <div class="mb-4">
            <label class="font-semibold mr-2">Filter Status:</label>
            <select v-model="filterStatus" class="border rounded px-3 py-1">
                <option value="Semua">Semua</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Diterima">Diterima</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div> -->
        <div class="p-6 bg-gray-100 min-h-screen pt-28">
            <TambahBarangMasuk
                :editData="editItem"
                @refresh="refreshBarangMasuk"
                @resetEdit="editItem = null"
            />

            <DaftarBarangMasuk
                :barangMasuk="barangMasukBulanIniFiltered"
                @edit="onEditBarang"
                @delete="hapusBarang"
                :role="role"
                @update-status="updateStatusBarang"
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
            role: "",
            barangMasuk: [],
            filterStatus: "Semua",
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
        this.role = role;
        this.getBarangMasuk();
    },
    methods: {
        sortBarangMasuk() {
            const priority = {
                Menunggu: 0,
                Ditolak: 1,
                Diterima: 2,
            };

            this.barangMasuk.sort((a, b) => {
                if (priority[a.status] !== priority[b.status]) {
                    return priority[a.status] - priority[b.status];
                }
                return new Date(b.created_at) - new Date(a.created_at);
            });
        },
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
                this.barangMasuk.splice(index, 1, {
                    ...this.barangMasuk[index],
                    ...dataBaru,
                });
            } else {
                // INI TIDAK BOLEH TERJADI SAAT EDIT
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
        updateStatusBarang({ item, status }) {
            axios
                .put(`/api/barang-masuk/${item.id}/status`, {
                    status: status,
                })
                .then((res) => {
                    const updated = res.data.data;

                    const index = this.barangMasuk.findIndex(
                        (b) => b.id === updated.id
                    );

                    if (index !== -1) {
                        this.barangMasuk.splice(index, 1, updated);
                    }
                })
                .catch((err) => {
                    console.error(err.response?.data || err);
                    alert("Gagal update status");
                });
        },

        goBack() {
            const role = localStorage.getItem("role");
            if (role === "pemilik_toko") {
                this.$router.push("/beranda-pemilik");
            } else {
                this.$router.push("/beranda-karyawan");
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
    computed: {
        barangMasukBulanIniFiltered() {
            const now = new Date();
            const bulanSekarang = now.getMonth();
            const tahunSekarang = now.getFullYear();

            return (
                this.barangMasuk
                    // 1️⃣ Filter bulan & tahun
                    .filter((item) => {
                        const tgl = new Date(item.created_at);
                        return (
                            tgl.getMonth() === bulanSekarang &&
                            tgl.getFullYear() === tahunSekarang
                        );
                    })
                    // 2️⃣ Filter status
                    .filter((item) => {
                        if (this.filterStatus === "Semua") return true;
                        return item.status === this.filterStatus;
                    })
            );
        },
    },
};
</script>
