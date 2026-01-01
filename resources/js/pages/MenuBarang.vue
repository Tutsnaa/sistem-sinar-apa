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
                                <td class="border px-4 py-2">
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
    </div>
</template>

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

            // 🔼 sorting: status HABIS di atas
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
        async hapusBarang(id) {
            if (!confirm("Yakin ingin menghapus barang ini?")) return;

            try {
                await axios.delete(`/api/barang/${id}`);

                this.daftarBarang = this.daftarBarang.filter(
                    (barang) => barang.id !== id
                );

                alert("Barang berhasil dihapus");
            } catch (error) {
                console.error(error);
                alert("Gagal menghapus barang");
            }
        },

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
