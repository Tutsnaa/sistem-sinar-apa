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
                <!-- Form Tambah & Pencarian dalam satu baris -->
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
        <!-- PESAN ERROR -->
        <p
            v-if="error"
            class="absolute left-0 top-full mt-0.5 text-[15px] text-red-500 z-50"
        >
            {{ error }}
        </p>
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
                this.daftarKategori = response.data.data;
            } catch (error) {
                console.error("Gagal mengambil data kategori:", error);
            }
        },
        async tambahKategoriKeDaftar(kategoriBaru) {
            this.errorNama = "";

            try {
                const response = await axios.post("api/kategori", kategoriBaru);

                if (response.data.success) {
                    // tampilkan kategori baru di atas
                    this.daftarKategori.unshift(response.data.data);

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

        // klik tombol Ubah
        setEditKategori(kategori) {
            this.isEdit = true;
            this.kategoriEdit = { ...kategori };
        },

        // update kategori (PUT)
        async updateKategori(data) {
            try {
                const response = await axios.put(`api/kategori/${data.id}`, {
                    nama_kategori: data.nama_kategori,
                });

                if (response.data.success) {
                    const index = this.daftarKategori.findIndex(
                        (k) => k.id === data.id
                    );
                    this.daftarKategori[index] = response.data.data;
                    this.batalEdit();
                }
                console.log(response.data);
            } catch (error) {
                alert(
                    error.response?.data?.message || "Gagal mengubah kategori!"
                );
            }
        },

        batalEdit() {
            this.isEdit = false;
            this.kategoriEdit = null;
        },

        async hapusKategori(id) {
            if (!confirm("Apakah yakin ingin menghapus kategori ini?")) return;
            try {
                const response = await axios.delete(`api/kategori/${id}`);
                if (response.data.success)
                    this.daftarKategori = this.daftarKategori.filter(
                        (k) => k.id !== id
                    );

                console.log(response.data);
            } catch (error) {
                alert(
                    error.response?.data?.message || "Gagal menghapus kategori!"
                );
            }
        },
    },
};
</script>

<style scoped>
/* opsional styling tambahan */
</style>
