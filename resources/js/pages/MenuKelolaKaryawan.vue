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

        <div class="bg-white p-4 rounded shadow mt-5 mb-6">
            <div class="flex justify-between items-center">
                <button
                    @click="bukaTambahKaryawan"
                    class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
                >
                    + Tambah Karyawan
                </button>
                <input
                    v-model="search"
                    @input="fetchKaryawan"
                    type="text"
                    placeholder="Cari karyawan..."
                    class="border px-3 py-2 rounded w-1/3"
                />
            </div>
        </div>

        <TambahKaryawan
            v-show="showTambahModal"
            @close="showTambahModal = false"
            @refresh="fetchKaryawan"
            @success="onTambahSuccess"
            @error="onTambahError"
        />

        <div class="bg-white p-4 rounded shadow mt-8 mb-6">
            <h2 class="text-lg font-bold mb-4">Daftar Karyawan</h2>
            <div class="max-h-[500px] overflow-y-auto border rounded">
                <table
                    class="w-full bg-white border border-gray-300 rounded-lg"
                >
                    <thead class="bg-gray-100 sticky top-0 z-20">
                        <tr>
                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Foto</th>
                            <th class="border px-4 py-2">Nama Lengkap</th>
                            <th class="border px-4 py-2">Email</th>
                            <th class="border px-4 py-2">No. Telepon</th>
                            <th class="border px-4 py-2">Nama Pengguna</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(karyawan, index) in karyawanList"
                            :key="karyawan.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border px-4 py-2 text-center">
                                {{ index + 1 }}
                            </td>
                            <td class="border px-4 py-2 text-center">
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

                            <td class="border px-4 py-2">
                                {{ karyawan.nama_lengkap }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ karyawan.email }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ karyawan.no_telepon }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ karyawan.nama_pengguna }}
                            </td>
                            <td class="border px-6 py-2 text-center">
                                <label
                                    class="inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only"
                                        :checked="karyawan.status === 'aktif'"
                                        @change="toggleStatus(karyawan)"
                                    />

                                    <!-- SWITCH -->
                                    <div
                                        class="relative w-20 h-6 rounded-full transition"
                                        :class="
                                            karyawan.status === 'aktif'
                                                ? 'bg-green-500'
                                                : 'bg-red-500'
                                        "
                                    >
                                        <!-- TEXT -->
                                        <span
                                            class="absolute text-xs font-semibold text-white top-1/2 -translate-y-1/2 transition"
                                            :class="
                                                karyawan.status === 'aktif'
                                                    ? 'left-2'
                                                    : 'right-2'
                                            "
                                        >
                                            {{
                                                karyawan.status === "aktif"
                                                    ? "Aktif"
                                                    : "Nonaktif"
                                            }}
                                        </span>

                                        <!-- KNOB -->
                                        <div
                                            class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 transition"
                                            :class="
                                                karyawan.status === 'aktif'
                                                    ? 'translate-x-14'
                                                    : 'translate-x-0.5'
                                            "
                                        ></div>
                                    </div>
                                </label>
                            </td>

                            <td class="border px-4 py-2">
                                <div class="flex justify-center gap-2">
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
                                </div>
                            </td>
                        </tr>
                        <tr v-if="karyawanList.length === 0">
                            <td
                                colspan="6"
                                class="text-center py-4 text-gray-500"
                            >
                                Data karyawan tidak ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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

                <!-- Nama Pengguna -->
                <div class="flex flex-col">
                    <label class="text-gray-600 text-sm mb-1"
                        >Nama Pengguna</label
                    >
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
                        placeholder="Kosongkan jika tidak mengganti"
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
                    class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
                >
                    Batal
                </button>
                <button
                    @click="updateKaryawan"
                    class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
    <!-- NOTIFICATION -->
    <div
        v-if="toast"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-50 text-white px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down"
        :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
    >
        <!-- ICON SUCCESS -->
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

        <!-- ICON ERROR -->
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
            toast: false, // untuk toggle tampil/tidak
            toastMessage: "", // pesan yang ditampilkan
            toastType: "success", // 'success' atau 'error'
            confirmDelete: { show: false },
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
        onTambahSuccess(pesan) {
            this.showTambahModal = false;
            this.fetchKaryawan();
            this.showToast(pesan, "success");
        },
        onTambahError(pesan) {
            this.showToast(pesan, "error");
        },
        showToast(message, type = "success") {
            this.toastMessage = message;
            this.toastType = type;
            this.toast = true;

            setTimeout(() => {
                this.toast = false;
            }, 3000); // toast otomatis hilang setelah 3 detik
        },

        async toggleStatus(karyawan) {
            if (!karyawan || !karyawan.status) return;

            // Cegah nonaktif pemilik toko
            if (karyawan.role === "pemilik_toko") {
                this.showToast("Pemilik toko tidak bisa dinonaktifkan");
                return;
            }

            const statusBaru =
                karyawan.status === "aktif" ? "nonaktif" : "aktif";

            try {
                await axios.patch(`/api/pengguna/${karyawan.id}/${statusBaru}`);

                // Update UI langsung
                karyawan.status = statusBaru;
            } catch (error) {
                console.error(error);
                alert("Gagal mengubah status karyawan");
            }
        },

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
            this.confirmDelete = {
                show: true,
                id,
            };
        },
        async confirmHapus() {
            try {
                await axios.delete(`/api/karyawan/${this.confirmDelete.id}`);
                console.log("Data karyawan berhasil dihapus");

                // Refresh data karyawan
                this.fetchKaryawan();

                // Toast sukses
                this.showToast("Data karyawan berhasil dihapus", "success");
            } catch (error) {
                console.error(error);

                // Toast gagal
                this.showToast("Gagal menghapus data karyawan", "error");
            } finally {
                // Tutup modal & reset state
                this.confirmDelete.show = false;
                this.confirmDelete.id = null;
            }
        },

        // hapusKaryawan(id) {
        //     if (
        //         confirm("Apakah yakin ingin menghapus karyawan ini?", "success")
        //     ) {
        //         axios
        //             .delete(`/api/karyawan/${id}`)
        //             .then(() => {
        //                 this.fetchKaryawan();
        //                 this.showToast(
        //                     "Data karyawan berhasil dihapus",
        //                     "success"
        //                 );
        //             })
        //             .catch((err) => {
        //                 console.error(err);
        //                 this.showToast(
        //                     "Gagal menghapus data karyawan",
        //                     "error"
        //                 );
        //             });
        //     }
        // },

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
                    console.log(res);
                    this.showEditModal = false;
                    this.karyawanEdit = null;
                    this.fetchKaryawan();

                    this.showToast("Data karyawan berhasil diubah", "success");
                })
                .catch((err) => {
                    console.error(err);

                    this.showToast("Gagal mengubah data karyawan", "error");
                });
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
