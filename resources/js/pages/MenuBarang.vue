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
                <span class="material-icons" style="font-size: 25px">
                    arrow_back
                </span>
                Kembali
            </h1>
            <h1 class="text-xl font-semibold">BARANG</h1>
            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- ===== KONTEN ===== -->
        <div class="p-6">
            <div class="bg-white rounded-lg shadow p-4">
                <!-- Bar atas: tombol + pencarian -->
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4"
                >
                    <!-- Tombol Tambah -->
                    <button
                        class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded w-fit"
                        @click="showForm = true"
                    >
                        + Tambah Barang
                    </button>

                    <FormTambahBarang
                        v-if="showForm"
                        :kategori="kategori"
                        @close="showForm = false"
                        @success="getBarang"
                    />

                    <!-- Pencarian -->
                    <input
                        v-model="cari"
                        type="text"
                        placeholder="Cari nama barang..."
                        class="border border-gray-300 rounded px-3 py-2 w-full md:max-w-xs"
                    />
                </div>

                <!-- Tabel Barang -->
                <div class="max-h-[500px] overflow-y-auto border rounded">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100 sticky top-0 z-20">
                            <tr>
                                <th class="border px-4 py-2 text-center">No</th>
                                <th class="border px-4 py-2 text-center">
                                    Nama Barang
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Satuan
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Kategori
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Harga Beli
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Harga Jual
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Jumlah
                                </th>
                                <th class="border px-4 py-2 text-center">
                                    Status
                                </th>
                                <th class="border pr-4 py-2 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="barang in filteredBarang"
                                :key="barang.id"
                                class="border-b hover:bg-gray-50"
                            >
                                <td class="border px-4 py-2 text-center">
                                    {{ filteredBarang.indexOf(barang) + 1 }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ barang.nama_barang }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    {{ barang.satuan }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ barang.kategori?.nama_kategori }}
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    Rp
                                    {{
                                        Number(
                                            barang.harga_beli
                                        ).toLocaleString("id-ID")
                                    }}
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    Rp
                                    {{
                                        Number(
                                            barang.harga_jual
                                        ).toLocaleString("id-ID")
                                    }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    {{ barang.jumlah }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <span
                                        :class="
                                            barang.status === 'tersedia'
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{
                                            barang.status
                                                .charAt(0)
                                                .toUpperCase() +
                                            barang.status.slice(1)
                                        }}
                                    </span>
                                </td>
                                <td class="border px-4 py-2">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            class="bg-yellow-500 text-white hover:bg-yellow-600 px-3 py-1 rounded"
                                            @click="
                                                barangDipilih = barang;
                                                showEdit = true;
                                            "
                                        >
                                            Ubah
                                        </button>
                                        <button
                                            class="bg-[#DC3545] text-white hover:bg-[#B02A37] px-3 py-1 rounded"
                                            @click="hapusBarang(barang.id)"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!filteredBarang.length">
                                <td colspan="8" class="text-center py-4">
                                    Data barang belum ada
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <FormUbahBarang
                v-if="showEdit"
                :barang="barangDipilih"
                :kategori="kategori"
                @close="showEdit = false"
                @success="getBarang"
            />
        </div>
        <!-- NOTIFICATION -->
        <div
            v-if="toast"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down"
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
                {{ toast }}
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
import FormTambahBarang from "@/components/FormTambahBarang.vue";
import FormUbahBarang from "@/components/FormUbahBarang.vue";

export default {
    components: { FormTambahBarang, FormUbahBarang },
    data() {
        return {
            nama: "",
            foto: "",
            daftarBarang: [],
            cari: "",
            showForm: false,
            kategori: [],
            showEdit: false,
            barangDipilih: null,
            toast: "",

            confirmDelete: {
                show: false,
                id: null,
            },
        };
    },

    computed: {
        filteredBarang() {
            let data = [...this.daftarBarang];

            // 🔍 filter pencarian
            if (this.cari) {
                data = data.filter((b) =>
                    b.nama_barang
                        .toLowerCase()
                        .includes(this.cari.toLowerCase())
                );
            }

            //sorting: status HABIS di atas
            return data.sort((a, b) => {
                if (a.status === "habis" && b.status !== "habis") return -1;
                if (a.status !== "habis" && b.status === "habis") return 1;
                return 0;
            });
        },
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
        this.getBarang();
        this.getKategori();
    },

    methods: {
        showToast(pesan) {
            this.toast = pesan;
            setTimeout(() => {
                this.toast = "";
            }, 3000);
        },

        hapusBarang(id) {
            this.confirmDelete = {
                show: true,
                id,
            };
        },

        async confirmHapus() {
            try {
                await axios.delete(`/api/barang/${this.confirmDelete.id}`);

                this.daftarBarang = this.daftarBarang.filter(
                    (barang) => barang.id !== this.confirmDelete.id
                );

                this.showToast("Barang berhasil dihapus", "success");
            } catch (error) {
                this.showToast("Gagal menghapus barang", "error");
            } finally {
                this.confirmDelete.show = false;
                this.confirmDelete.id = null;
            }
        },

        // async hapusBarang(id) {
        //     if (!confirm("Yakin ingin menghapus barang ini?")) return;

        //     try {
        //         await axios.delete(`/api/barang/${id}`);

        //         this.daftarBarang = this.daftarBarang.filter(
        //             (barang) => barang.id !== id
        //         );

        //         this.showToast("Barang berhasil dihapus");
        //     } catch (error) {
        //         console.error(error);
        //         this.showToast("Gagal menghapus barang");
        //     }
        // },

        goBack() {
            const role = localStorage.getItem("role");
            if (role === "pemilik_toko") this.$router.push("/beranda-pemilik");
            else if (role === "karyawan")
                this.$router.push("/beranda-karyawan");
            else this.$router.push("/login");
        },

        async getBarang() {
            try {
                const res = await axios.get("http://127.0.0.1:8000/api/barang");

                console.log(res.data);

                this.daftarBarang = res.data.data ?? res.data ?? [];
            } catch (error) {
                console.error(error);
                this.daftarBarang = [];
            }
        },

        async getKategori() {
            const res = await axios.get("/api/kategori");
            this.kategori = res.data.data;
        },

        formatRupiah(number) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
            }).format(number);
        },
        formatRupiah(number) {
            if (number === null || number === undefined) return "Rp 0";

            return "Rp " + Number(number).toLocaleString("id-ID");
        },
    },
};
</script>
