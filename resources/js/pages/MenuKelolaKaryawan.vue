<template>
    <div class="min-h-screen bg-gray-100 pt-24 px-6">
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
            <h1 class="text-xl font-semibold">KELOLA KARYAWAN</h1>
            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <div class="flex justify-between items-center mb-4">
            <input
                v-model="search"
                @input="fetchKaryawan"
                type="text"
                placeholder="Cari karyawan..."
                class="border px-3 py-2 rounded w-1/3"
            />

            <!-- ✅ SATU-SATUNYA TOMBOL -->
            <button
                @click="bukaTambahKaryawan"
                class="bg-[#3674B5] text-white px-4 py-2 rounded hover:bg-blue-600"
            >
                Tambah Karyawan
            </button>
        </div>

        <TambahKaryawan
            v-show="showTambahModal"
            @close="showTambahModal = false"
            @refresh="fetchKaryawan"
        />
        <div class="max-h-[500px] overflow-y-auto border rounded">
            <table class="w-full bg-white border border-gray-300 rounded-lg">
                <thead class="bg-gray-100 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-2 border-b">No</th>
                        <th class="px-4 py-2 border-b">Foto</th>
                        <th class="px-4 py-2 border-b">Nama Lengkap</th>
                        <th class="px-4 py-2 border-b">Email</th>
                        <th class="px-4 py-2 border-b">No. Telepon</th>
                        <th class="px-4 py-2 border-b">Username</th>
                        <th class="px-4 py-2 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(karyawan, index) in karyawanList"
                        :key="karyawan.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2 border-b">{{ index + 1 }}</td>
                        <td class="px-4 py-2 border-b text-center">
                            <img
                                v-if="karyawan.foto"
                                :src="`/foto_pengguna/${karyawan.foto}`"
                                alt="Foto Karyawan"
                                class="w-12 h-12 rounded-full object-cover mx-auto"
                            />
                            <span v-else class="text-gray-400"
                                >Tidak ada foto</span
                            >
                        </td>

                        <td class="px-4 py-2 border-b">
                            {{ karyawan.nama_lengkap }}
                        </td>
                        <td class="px-4 py-2 border-b">{{ karyawan.email }}</td>
                        <td class="px-4 py-2 border-b">
                            {{ karyawan.no_telepon }}
                        </td>
                        <td class="px-4 py-2 border-b">
                            {{ karyawan.nama_pengguna }}
                        </td>
                        <td class="px-4 py-2 border-b flex gap-2">
                            <button
                                @click="editKaryawan(karyawan)"
                                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition"
                            >
                                Ubah
                            </button>
                            <button
                                @click="hapusKaryawan(karyawan.id)"
                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <tr v-if="karyawanList.length === 0">
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            Data karyawan tidak ditemukan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal Edit Karyawan -->
    <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-semibold mb-4">Ubah Karyawan</h2>

            <div class="flex flex-col gap-4">
                <!-- Nama Lengkap -->
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm mb-1"
                        >Nama Lengkap</label
                    >
                    <input
                        v-model="karyawanEdit.nama_lengkap"
                        placeholder="Masukkan nama lengkap karyawan"
                        class="border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Email -->
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm mb-1">Email</label>
                    <input
                        v-model="karyawanEdit.email"
                        type="email"
                        placeholder="Masukkan email karyawan"
                        class="border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- No. Telepon -->
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm mb-1"
                        >No. Telepon</label
                    >
                    <input
                        v-model="karyawanEdit.no_telepon"
                        type="tel"
                        placeholder="Masukkan nomor telepon karyawan"
                        class="border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <!-- Username -->
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm mb-1">Username</label>
                    <input
                        v-model="karyawanEdit.nama_pengguna"
                        placeholder="Masukkan username karyawan"
                        class="border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>

                <div class="flex flex-col relative">
                    <label class="text-gray-600 text-sm mb-1"
                        >Kata Sandi Baru</label
                    >
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="karyawanEdit.kata_sandi"
                        placeholder="Kosongkan jika tidak mengganti password"
                        class="border px-3 py-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                    <span
                        class="absolute right-3 top-12 transform -translate-y-1/2 cursor-pointer text-gray-500"
                        @click="toggleShowPassword"
                    >
                        <span class="material-icons">
                            {{ showPassword ? "visibility_off" : "visibility" }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Tombol Batal dan Simpan -->
            <div class="flex justify-end gap-2 mt-6">
                <button
                    @click="showEditModal = false"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                >
                    Batal
                </button>
                <button
                    @click="updateKaryawan"
                    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import TambahKaryawan from "../components/TambahKaryawan.vue";

export default {
    components: {
        TambahKaryawan,
    },
    data() {
        return {
            karyawanList: [],
            search: "",
            nama: "",
            foto: "",
            karyawanEdit: null,
            showEditModal: false,
            showPassword: false,
            showTambahModal: false,
        };
    },
    mounted() {
        this.nama = localStorage.getItem("nama");
        this.foto = localStorage.getItem("foto");
        const role = localStorage.getItem("role");

        if (!this.nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.fetchKaryawan();
    },
    methods: {
        bukaTambahKaryawan() {
            console.log("Tombol diklik"); // 🔍 DEBUG
            this.showTambahModal = true;
        },
        tutupTambahKaryawan() {
            this.showTambahModal = false;
        },

        toggleShowPassword() {
            this.showPassword = !this.showPassword;
        },

        fetchKaryawan() {
            axios
                .get("/api/karyawan", { params: { search: this.search } })
                .then((res) => {
                    this.karyawanList = res.data.data; // sesuaikan, karena response ada di key 'data'
                })
                .catch((err) => console.error(err));
        },

        tambahKaryawan() {
            this.$router.push("/tambah-karyawan"); // bisa diarahkan ke halaman tambah karyawan
        },
        hapusKaryawan(id) {
            if (confirm("Apakah yakin ingin menghapus karyawan ini?")) {
                axios
                    .delete(`/api/karyawan/${id}`)
                    .then(() => {
                        this.fetchKaryawan();
                    })
                    .catch((err) => console.error(err));
            }
        },

        editKaryawan(karyawan) {
            this.karyawanEdit = {
                id: karyawan.id,
                nama_lengkap: karyawan.nama_lengkap,
                email: karyawan.email,
                no_telepon: karyawan.no_telepon,
                nama_pengguna: karyawan.nama_pengguna,
                kata_sandi: "", // **PASTIKAN KOSONG**
            };
            this.showPassword = false; // reset show/hide
            this.showEditModal = true;
        },

        updateKaryawan() {
            const payload = { ...this.karyawanEdit };

            // Hapus field password jika kosong agar Laravel tidak overwrite
            if (!payload.kata_sandi) {
                delete payload.kata_sandi;
            }

            axios
                .put(`/api/karyawan/${payload.id}`, payload)
                .then((res) => {
                    this.showEditModal = false;
                    this.karyawanEdit = null;
                    this.fetchKaryawan();
                })
                .catch((err) => console.error(err));
        },

        goBack() {
            const role = localStorage.getItem("role");
            if (role === "pemilik_toko") this.$router.push("/beranda-pemilik");
            else if (role === "karyawan")
                this.$router.push("/beranda-karyawan");
            else this.$router.push("/login");
        },
    },
};
</script>
