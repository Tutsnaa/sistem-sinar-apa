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
                >
                Kembali
            </h1>
            <h1 class="text-xl font-semibold">KATEGORI BARANG</h1>
            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- Konten -->
        <div class="p-6 pt-28">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4"
                >
                    <!-- Form Tambah Kategori -->
                    <FormTambahKategori
                        :isEdit="isEdit"
                        :dataEdit="kategoriEdit"
                        @tambah-kategori="tambahKategoriKeDaftar"
                        @update-kategori="updateKategori"
                        @batal-edit="batalEdit"
                        class="flex-1 max-w-sm"
                        :error="errorNama"
                    />
                    <!-- Input Pencarian -->
                    <input
                        type="text"
                        v-model="cari"
                        placeholder="Cari kategori..."
                        class="flex-1 max-w-xs mt-9 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Daftar Kategori -->
                <DaftarKategori
                    :kategoriList="filteredKategori"
                    @hapus="hapusKategori"
                    @ubah="setEditKategori"
                />
            </div>
        </div>

        <!-- CONFIRM DELETE -->
        <div
            v-if="confirmDelete.show"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-white px-6 py-4 rounded-xl shadow-xl w-[320px] animate-slide-down"
        >
            <p class="text-sm font-medium text-gray-800 mb-4 text-center">
                Yakin ingin menghapus kategori ini?
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

        <!-- NOTIFICATION -->
        <div
            v-if="toast"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down"
            :class="
                toastType === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            "
        >
            <!-- Icon -->
            <svg
                v-if="toastType === 'success'"
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
                    d="M5 13l4 4L19 7"
                />
            </svg>
            <svg
                v-else
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
            <span class="font-medium text-sm">{{ toast }}</span>
        </div>
    </div>
</template>

<script>
import FormTambahKategori from "../components/FormTambahKategori.vue";
import DaftarKategori from "../components/DaftarKategori.vue";
import axios from "axios";

export default {
    name: "MenuKategori",
    components: { FormTambahKategori, DaftarKategori },
    data() {
        return {
            nama: "",
            foto: "",
            daftarKategori: [],
            cari: "",
            isEdit: false,
            kategoriEdit: null,
            errorNama: "",
            toast: "",
            toastType: "success",
            confirmDelete: { show: false, id: null },
        };
    },
    computed: {
        filteredKategori() {
            if (!this.cari) return this.daftarKategori;
            return this.daftarKategori.filter((k) =>
                k.nama_kategori.toLowerCase().includes(this.cari.toLowerCase())
            );
        },
    },
    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");
        if (!nama || !role) this.$router.push("/login");
        this.nama = nama;
        this.foto = foto;
        this.getKategori();
    },
    methods: {
        showToast(pesan, type = "success") {
            this.toast = pesan;
            this.toastType = type;
            setTimeout(() => {
                this.toast = "";
                this.toastType = "success";
            }, 3000);
        },
        goBack() {
            const role = localStorage.getItem("role");
            if (role === "pemilik_toko") this.$router.push("/beranda-pemilik");
            else if (role === "karyawan")
                this.$router.push("/beranda-karyawan");
            else this.$router.push("/login");
        },
        async getKategori() {
            try {
                const response = await axios.get("api/kategori");
                // urutkan data terbaru paling atas (dari id terbesar ke terkecil)
                this.daftarKategori = response.data.data.sort(
                    (a, b) => b.id - a.id
                );
            } catch (error) {
                console.error("Gagal mengambil data kategori:", error);
            }
        },
        async tambahKategoriKeDaftar(kategoriBaru) {
            this.errorNama = "";
            try {
                const response = await axios.post("api/kategori", kategoriBaru);
                if (response.data.success) {
                    await this.getKategori(); // refresh daftar
                    this.showToast("Kategori berhasil ditambahkan", "success");
                }
            } catch (error) {
                const pesan =
                    error.response?.data?.errors?.nama_kategori?.[0] ||
                    error.response?.data?.message ||
                    "Gagal menambahkan kategori!";
                this.showToast(pesan, "error");
            }
        },
        setEditKategori(kategori) {
            this.isEdit = true;
            this.kategoriEdit = { ...kategori };
        },
        async updateKategori(data) {
            try {
                const response = await axios.put(`api/kategori/${data.id}`, {
                    nama_kategori: data.nama_kategori,
                });
                if (response.data.success) {
                    this.batalEdit();
                    await this.getKategori(); // refresh daftar terbaru di atas
                    this.showToast("Kategori berhasil diubah", "success");
                }
            } catch (error) {
                const pesan =
                    error.response?.data?.message || "Gagal mengubah kategori!";
                this.showToast(pesan, "error");
            }
        },
        batalEdit() {
            this.isEdit = false;
            this.kategoriEdit = null;
        },
        hapusKategori(id) {
            this.confirmDelete = { show: true, id };
        },
        async confirmHapus() {
            try {
                const response = await axios.delete(
                    `api/kategori/${this.confirmDelete.id}`
                );
                if (response.data.success) {
                    await this.getKategori(); // refresh daftar terbaru
                    this.showToast("Kategori berhasil dihapus", "success");
                } else {
                    this.showToast("Gagal menghapus kategori", "error");
                }
            } catch (error) {
                this.showToast(
                    error.response?.data?.message || "Gagal menghapus kategori",
                    "error"
                );
            } finally {
                this.confirmDelete.show = false;
                this.confirmDelete.id = null;
            }
        },
    },
};
</script>
