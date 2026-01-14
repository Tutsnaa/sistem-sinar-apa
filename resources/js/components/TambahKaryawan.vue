<template>
    <!-- Modal Tambah Karyawan -->
    <div
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded w-100 relative shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Tambah Karyawan</h2>

            <!-- Tombol Close -->
            <button
                @click="handleClose"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
            >
                <span class="material-icons">close</span>
            </button>

            <!-- FORM -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NAMA LENGKAP (FULL) -->
                <div class="md:col-span-2 flex flex-col space-y-1">
                    <label class="text-sm font-medium">Nama Lengkap</label>
                    <input
                        ref="namaLengkap"
                        v-model="karyawan.nama_lengkap"
                        @input="validateNama"
                        @keyup.enter="focusNext('email')"
                        :class="inputClass(errors.nama_lengkap)"
                    />

                    <span
                        v-if="errors.nama_lengkap"
                        class="text-xs text-red-500"
                    >
                        {{ errors.nama_lengkap }}
                    </span>
                </div>

                <!-- EMAIL (FULL) -->
                <div class="md:col-span-2 flex flex-col space-y-1">
                    <label class="text-sm font-medium">Email</label>
                    <input
                        ref="email"
                        v-model="karyawan.email"
                        type="email"
                        @input="validateEmail"
                        @keyup.enter="focusNext('noTelepon')"
                        :class="inputClass(errors.email)"
                    />

                    <span v-if="errors.email" class="text-xs text-red-500">
                        {{ errors.email }}
                    </span>
                </div>

                <!-- NO TELEPON (KIRI) -->
                <div class="flex flex-col space-y-1">
                    <label class="text-sm font-medium">No Telepon</label>
                    <input
                        ref="noTelepon"
                        v-model="karyawan.no_telepon"
                        @keypress="onlyNumber"
                        @input="validateTelepon"
                        @keyup.enter="focusNext('namaPengguna')"
                        :class="inputClass(errors.no_telepon)"
                    />

                    <span v-if="errors.no_telepon" class="text-xs text-red-500">
                        {{ errors.no_telepon }}
                    </span>
                </div>

                <!-- NAMA PENGGUNA (KANAN) -->
                <div class="flex flex-col space-y-1">
                    <label class="text-sm font-medium">Nama Pengguna</label>
                    <input
                        ref="namaPengguna"
                        v-model="karyawan.nama_pengguna"
                        @input="validateUsername"
                        @keyup.enter="focusNext('kataSandi')"
                        :class="inputClass(errors.nama_pengguna)"
                    />

                    <span
                        v-if="errors.nama_pengguna"
                        class="text-xs text-red-500"
                    >
                        {{ errors.nama_pengguna }}
                    </span>
                </div>

                <!-- KATA SANDI (KIRI) -->
                <div class="flex flex-col space-y-1 relative">
                    <label class="text-sm font-medium">Kata Sandi</label>
                    <input
                        ref="kataSandi"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="karyawan.kata_sandi"
                        @input="validatePassword"
                        @keyup.enter="focusNext('role')"
                        :class="inputClass(errors.kata_sandi)"
                    />

                    <span v-if="errors.kata_sandi" class="text-xs text-red-500">
                        {{ errors.kata_sandi }}
                    </span>

                    <span
                        class="absolute right-3 top-7 cursor-pointer text-gray-500"
                        @click="showPassword = !showPassword"
                    >
                        <span class="material-icons text-base">
                            {{ showPassword ? "visibility_off" : "visibility" }}
                        </span>
                    </span>
                </div>

                <!-- ROLE (KANAN) -->
                <div class="flex flex-col space-y-1">
                    <label class="text-sm font-medium">Role</label>
                    <select
                        ref="role"
                        v-model="karyawan.role"
                        @change="validateRole"
                        @keyup.enter="focusNext('foto')"
                        :class="inputClass(errors.role)"
                    >
                        <option value="">Pilih Role</option>
                        <option value="pemilik_toko">Pemilik Toko</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                    <span v-if="errors.role" class="text-xs text-red-500">
                        {{ errors.role }}
                    </span>
                </div>
            </div>

            <!-- Upload Foto -->
            <div class="md:col-span-2 flex flex-col space-y-1 mt-2">
                <label class="text-sm font-medium">Foto</label>

                <!-- WRAPPER -->
                <div
                    :class="[
                        'border rounded px-3 py-2 w-full',
                        errors.foto
                            ? 'border-red-500 ring-1 ring-red-500'
                            : 'border-gray-300',
                    ]"
                >
                    <input
                        ref="foto"
                        type="file"
                        accept="image/*"
                        @change="handleFileChange"
                        class="w-full text-sm outline-none"
                    />
                </div>

                <!-- PESAN ERROR -->
                <span v-if="errors.foto" class="text-xs text-red-500">
                    {{ errors.foto }}
                </span>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end gap-2 mt-6">
                <button
                    @click="handleClose"
                    class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
                >
                    Batal
                </button>
                <button
                    @click="simpanKaryawan"
                    class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
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
                foto: null,
            },
            errors: {
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
                role: "",
                foto: "",
            },
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.$refs.namaLengkap?.focus();
        });
    },

    methods: {
        handleClose() {
            this.resetForm();
            this.$emit("close");
        },

        resetForm() {
            this.karyawan = {
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
                role: "",
                foto: null,
            };

            this.errors = {
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
                role: "",
                foto: "",
            };

            this.showPassword = false;

            this.$nextTick(() => {
                if (this.$refs.foto) {
                    this.$refs.foto.value = "";
                }
                this.$refs.namaLengkap?.focus();
            });
        },

        onlyNumber(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
            }
        },
        focusNext(refName) {
            this.$nextTick(() => {
                const el = this.$refs[refName];
                if (el) el.focus();
            });
        },

        inputClass(error) {
            return [
                "border px-3 py-2 rounded focus:outline-none",
                error
                    ? "border-red-500 focus:ring-1 focus:ring-red-500"
                    : "border-gray-300",
            ];
        },

        //    Error data kosong
        validateNama() {
            this.errors.nama_lengkap = this.karyawan.nama_lengkap
                ? ""
                : "Nama lengkap wajib diisi";
        },

        validateEmail() {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!this.karyawan.email) {
                this.errors.email = "Email wajib diisi";
            } else if (!regex.test(this.karyawan.email)) {
                this.errors.email = "Format email tidak valid";
            } else {
                this.errors.email = "";
            }
        },

        validateTelepon() {
            const regex = /^[0-9]{10,13}$/;

            if (!this.karyawan.no_telepon) {
                this.errors.no_telepon = "No telepon wajib diisi";
            } else if (!regex.test(this.karyawan.no_telepon)) {
                this.errors.no_telepon = "No telepon tidak valid";
            } else {
                this.errors.no_telepon = "";
            }
        },

        validateUsername() {
            this.errors.nama_pengguna = this.karyawan.nama_pengguna
                ? ""
                : "Nama pengguna wajib diisi";
        },

        validatePassword() {
            if (!this.karyawan.kata_sandi) {
                this.errors.kata_sandi = "Kata sandi wajib diisi";
            } else if (this.karyawan.kata_sandi.length < 6) {
                this.errors.kata_sandi = "Minimal 6 karakter";
            } else {
                this.errors.kata_sandi = "";
            }
        },

        validateRole() {
            this.errors.role = this.karyawan.role ? "" : "Role wajib dipilih";
        },

        validateFoto() {
            if (this.errors.foto) return false;
            return true;
        },

        validateAll() {
            this.validateNama();
            this.validateEmail();
            this.validateTelepon();
            this.validateUsername();
            this.validatePassword();
            this.validateRole();

            if (!this.karyawan.foto) {
                this.errors.foto = "Foto wajib diunggah";
            }

            return !Object.values(this.errors).some((e) => e !== "");
        },

        handleFileChange(e) {
            const file = e.target.files[0];
            this.errors.foto = "";

            if (!file) {
                this.errors.foto = "Foto wajib diunggah";
                this.karyawan.foto = null;
                return;
            }

            const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
            if (!allowedTypes.includes(file.type)) {
                this.errors.foto = "Format foto harus JPG atau PNG";
                e.target.value = "";
                return;
            }

            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                this.errors.foto = "Ukuran foto maksimal 2MB";
                e.target.value = "";
                return;
            }

            this.karyawan.foto = file;
        },

        //     const file = e.target.files[0];
        //     this.errors.foto = "";

        //     if (!file) {
        //         this.karyawan.foto = null;
        //         return;
        //     }

        //     // Validasi tipe file
        //     const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
        //     if (!allowedTypes.includes(file.type)) {
        //         this.errors.foto = "Format foto harus JPG atau PNG";
        //         e.target.value = "";
        //         return;
        //     }

        //     // Validasi ukuran (max 2MB)
        //     const maxSize = 2 * 1024 * 1024;
        //     if (file.size > maxSize) {
        //         this.errors.foto = "Ukuran foto maksimal 2MB";
        //         e.target.value = "";
        //         return;
        //     }

        //     this.karyawan.foto = file;
        // },

        simpanKaryawan() {
            if (!this.validateAll()) return;
            if (this.errors.foto) return;

            const formData = new FormData();
            formData.append("nama_lengkap", this.karyawan.nama_lengkap);
            formData.append("email", this.karyawan.email);
            formData.append("no_telepon", this.karyawan.no_telepon);
            formData.append("nama_pengguna", this.karyawan.nama_pengguna);
            formData.append("kata_sandi", this.karyawan.kata_sandi);
            formData.append("role", this.karyawan.role);

            if (this.karyawan.foto) {
                formData.append("foto", this.karyawan.foto);
            }

            axios
                .post("/api/pengguna", formData)
                .then((res) => {
                    console.log(res);
                    if (res.data.success) {
                        this.$emit("success", "Karyawan berhasil ditambahkan");
                        this.resetForm();
                        this.$emit("refresh");
                        this.$emit("close");
                    }
                })
                .catch(() => {
                    this.$emit("error", "Gagal menambahkan karyawan");
                });
        },

        handleFileChange(e) {
            this.karyawan.foto = e.target.files[0];
        },
    },
};
</script>
