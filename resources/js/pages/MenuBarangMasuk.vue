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

        <div class="p-6 bg-gray-100 min-h-screen mt-16">
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
        <!-- NOTIFICATION -->
        <div
            v-if="toast"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-50 text-white px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down"
            :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
        >
            <!-- Icon -->
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
                    d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"
                />
            </svg>

            <span class="font-medium text-sm">
                {{ toastMessage }}
            </span>
        </div>

        <!-- CONFIRM DELETE -->
        <div
            v-if="confirmDelete.show"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-white px-6 py-4 rounded-xl shadow-xl w-[320px] animate-slide-down"
        >
            <p class="text-sm font-medium text-gray-800 mb-4 text-center">
                Yakin ingin menghapus barang ini?
            </p>

            <div class="flex justify-center gap-3">
                <button
                    @click="confirmDelete.show = false"
                    class="px-4 py-2 text-sm rounded bg-gray-300 hover:bg-gray-400"
                >
                    Batal
                </button>
                <button
                    @click="confirmHapus"
                    class="px-4 py-2 text-sm rounded bg-red-500 text-white hover:bg-red-600"
                >
                    Hapus
                </button>
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
    animation: slideDown 0.35s ease-out;
}
</style>

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

            periodeAwal: "",
            periodeAkhir: "",

            toast: false, // untuk toggle tampil/tidak
            toastMessage: "", // pesan yang ditampilkan
            toastType: "success", // 'success' atau 'error'
            confirmDelete: {
                show: false,
                id: null,
            },
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
        showToast(message, type = "success") {
            this.toastMessage = message;
            this.toastType = type;
            this.toast = true;

            setTimeout(() => {
                this.toast = false;
            }, 3000); // toast otomatis hilang setelah 3 detik
        },
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
            this.confirmDelete = {
                show: true,
                id: item.id,
                nama: item.barang?.nama_barang || "Barang",
            };
        },

        async confirmHapus() {
            try {
                await axios.delete(
                    `/api/barang-masuk/${this.confirmDelete.id}`
                );

                this.showToast("Barang masuk berhasil dihapus", "success");

                this.getBarangMasuk();
            } catch (error) {
                console.error(error);
                this.showToast("Gagal menghapus barang masuk", "error");
            } finally {
                this.confirmDelete = { show: false };
            }
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
            return this.barangMasuk.filter((item) => {
                if (!item.created_at) return false;

                // ======================
                // FILTER STATUS
                // ======================
                if (
                    this.filterStatus !== "Semua" &&
                    item.status !== this.filterStatus
                ) {
                    return false;
                }

                // ======================
                // FILTER TANGGAL (FIX)
                // ======================
                const tanggalItem = new Date(item.created_at);

                const awal = this.periodeAwal
                    ? new Date(this.periodeAwal + "T00:00:00")
                    : null;

                const akhir = this.periodeAkhir
                    ? new Date(this.periodeAkhir + "T23:59:59")
                    : null;

                if (awal && tanggalItem < awal) return false;
                if (akhir && tanggalItem > akhir) return false;

                return true;
            });
        },
    },
};
</script>
