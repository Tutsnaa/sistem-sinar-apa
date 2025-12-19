<template>
    <!-- Modal Tambah Karyawan -->
    <div
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded w-96 relative shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Tambah Karyawan</h2>

            <!-- Tombol Close -->
            <button
                @click="$emit('close')"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
            >
                <span class="material-icons">close</span>
            </button>

            <!-- Form -->
            <div class="flex flex-col gap-3">
                <input
                    v-model="karyawan.nama_lengkap"
                    placeholder="Nama Lengkap"
                    class="border px-3 py-2 rounded"
                />
                <input
                    v-model="karyawan.email"
                    type="email"
                    placeholder="Email"
                    class="border px-3 py-2 rounded"
                />
                <input
                    v-model="karyawan.no_telepon"
                    placeholder="No Telepon"
                    class="border px-3 py-2 rounded"
                />
                <input
                    v-model="karyawan.nama_pengguna"
                    placeholder="Username"
                    class="border px-3 py-2 rounded"
                />

                <!-- Password -->
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="karyawan.kata_sandi"
                        placeholder="Password (min 6 karakter)"
                        class="border px-3 py-2 rounded w-full"
                    />
                    <span
                        class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer"
                        @click="showPassword = !showPassword"
                    >
                        <span class="material-icons">
                            {{ showPassword ? "visibility_off" : "visibility" }}
                        </span>
                    </span>
                </div>

                <select
                    v-model="karyawan.role"
                    class="border px-3 py-2 rounded"
                >
                    <option value="">Pilih Role</option>
                    <option value="pemilik_toko">Pemilik Toko</option>
                    <option value="karyawan">Karyawan</option>
                </select>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end gap-2 mt-6">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 bg-gray-300 rounded"
                >
                    Batal
                </button>
                <button
                    @click="simpanKaryawan"
                    class="px-4 py-2 bg-blue-500 text-white rounded"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    data() {
        return {
            showPassword: false,
            karyawan: {
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
                role: "",
            },
        };
    },
    methods: {
        simpanKaryawan() {
            axios
                .post("/api/karyawan", this.karyawan)
                .then(() => {
                    this.$emit("refresh");
                    this.$emit("close");
                })
                .catch((err) => {
                    console.error(err);
                    alert("Gagal menambahkan karyawan");
                });
        },
    },
};
</script>
