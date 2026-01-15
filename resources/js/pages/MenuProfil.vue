<template>
    <div class="min-h-screen bg-gray-100 pt-28 px-6">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow fixed top-0 left-0 w-full z-50"
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

            <h1 class="text-xl font-semibold">PROFIL</h1>

            <div class="flex items-center gap-4">
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- KONTEN PROFIL -->
        <div class="w-full bg-white shadow p-10">
            <div class="flex gap-8">
                <!-- FOTO (KIRI) -->
                <div class="w-1/4 flex flex-col">
                    <img
                        v-if="form.foto"
                        :src="`/foto_pengguna/${form.foto}`"
                        class="w-full h-[420px] object-cover border-2 border-gray-300 rounded-lg mb-4"
                    />
                    <div
                        v-else
                        class="w-full h-80 bg-gray-200 border-2 border-gray-300 rounded-lg flex items-center justify-center mb-4"
                    >
                        <span class="text-gray-600">No Foto</span>
                    </div>

                    <label class="text-sm font-semibold mb-1"
                        >Foto Profil</label
                    >
                    <input type="file" @change="onFileChange" class="text-sm" />
                </div>

                <!-- FORM (KANAN) -->
                <div class="flex-1">
                    <form @submit.prevent="updateProfil" class="space-y-5">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_lengkap"
                                class="input w-full"
                                placeholder="Masukkan nama lengkap"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Email
                            </label>
                            <input
                                type="email"
                                v-model="form.email"
                                class="input w-full"
                                placeholder="Masukkan email"
                            />
                        </div>

                        <!-- No Telepon -->
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                No Telepon
                            </label>
                            <input
                                type="text"
                                @keydown="onlyNumber"
                                v-model="form.no_telepon"
                                class="input w-full"
                                placeholder="Masukkan nomor telepon"
                            />
                        </div>

                        <!-- Nama Pengguna -->
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nama Pengguna
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_pengguna"
                                class="input w-full"
                                placeholder="Masukkan nama pengguna"
                            />
                        </div>

                        <!-- Kata Sandi -->
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Kata Sandi
                            </label>
                            <input
                                type="password"
                                v-model="form.kata_sandi"
                                class="input w-full"
                                placeholder="Kosongkan jika tidak ingin mengubah"
                            />
                        </div>

                        <!-- Tombol -->
                        <button
                            type="submit"
                            :disabled="!isChanged"
                            class="w-full py-3 rounded transition bg-[#1E5AA8] text-white hover:bg-[#164B8A] disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400"
                        >
                            {{
                                isChanged
                                    ? "Simpan Perubahan"
                                    : "Belum ada perubahan"
                            }}
                        </button>
                    </form>
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
                {{ toast }}
            </span>
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

export default {
    data() {
        return {
            nama: "",
            idPengguna: null,
            fotoBaru: null,
            form: {
                foto: "",
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
            },
            errors: {
                nama_lengkap: "",
                email: "",
                no_telepon: "",
                nama_pengguna: "",
                kata_sandi: "",
            },
            originalForm: {},
            toast: "",
            toastType: "success",
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const id = localStorage.getItem("id_pengguna");

        if (!nama || !role || !id) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.idPengguna = id;

        this.getProfil();
    },

    // computed: {
    //     isChanged() {
    //         const formCopy = { ...this.form };
    //         delete formCopy.kata_sandi; // password opsional

    //         const originalCopy = { ...this.originalForm };
    //         delete originalCopy.kata_sandi;

    //         return (
    //             JSON.stringify(formCopy) !== JSON.stringify(originalCopy) ||
    //             this.fotoBaru !== null
    //         );
    //     },
    // },
    computed: {
        isChanged() {
            if (!this.originalForm) return false;

            return (
                this.form.nama_lengkap !== this.originalForm.nama_lengkap ||
                this.form.email !== this.originalForm.email ||
                this.form.no_telepon !== this.originalForm.no_telepon ||
                this.form.nama_pengguna !== this.originalForm.nama_pengguna ||
                this.form.kata_sandi !== "" ||
                this.fotoBaru !== null
            );
        },
    },

    methods: {
        //ERROR VALIDATE
        validateNamaLengkap() {
            this.errors.nama_lengkap = this.form.nama_lengkap
                ? ""
                : "Nama lengkap wajib diisi";
        },

        validateEmail() {
            if (!this.form.email) {
                this.errors.email = "Email wajib diisi";
            } else if (!/\S+@\S+\.\S+/.test(this.form.email)) {
                this.errors.email = "Format email tidak valid";
            } else {
                this.errors.email = "";
            }
        },

        validateNoTelepon() {
            if (!this.form.no_telepon) {
                this.errors.no_telepon = "No telepon wajib diisi";
            } else if (!/^[0-9]{10,15}$/.test(this.form.no_telepon)) {
                this.errors.no_telepon = "No telepon harus angka (10–15 digit)";
            } else {
                this.errors.no_telepon = "";
            }
        },

        validateNamaPengguna() {
            this.errors.nama_pengguna = this.form.nama_pengguna
                ? ""
                : "Nama pengguna wajib diisi";
        },

        validateKataSandi() {
            if (!this.form.kata_sandi) {
                this.errors.kata_sandi = "";
            } else if (this.form.kata_sandi.length < 6) {
                this.errors.kata_sandi = "Kata sandi minimal 6 karakter";
            } else {
                this.errors.kata_sandi = "";
            }
        },

        validateForm() {
            this.validateNamaLengkap();
            this.validateEmail();
            this.validateNoTelepon();
            this.validateNamaPengguna();
            this.validateKataSandi();

            return !Object.values(this.errors).some((e) => e);
        },

        showToast(message, type = "success") {
            this.toast = message;
            this.toastType = type;

            setTimeout(() => {
                this.toast = "";
            }, 3000);
        },
        onlyNumber(e) {
            // izinkan: angka, backspace, delete, panah
            if (
                !/[0-9]/.test(e.key) &&
                ![
                    "Backspace",
                    "Delete",
                    "ArrowLeft",
                    "ArrowRight",
                    "Tab",
                ].includes(e.key)
            ) {
                e.preventDefault(); //BLOKIR HURUF
            }
        },

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

        async getProfil() {
            try {
                const res = await axios.get(`/api/pengguna/${this.idPengguna}`);
                const user = res.data.data;

                this.form.foto = user.foto;
                this.form.nama_lengkap = user.nama_lengkap;
                this.form.email = user.email;
                this.form.no_telepon = user.no_telepon;
                this.form.nama_pengguna = user.nama_pengguna;
                this.form.kata_sandi = "";

                // 🔥 SIMPAN DATA AWAL
                this.originalForm = JSON.parse(JSON.stringify(this.form));
            } catch (error) {
                this.showToast("Gagal mengambil data profil", "error");
            }
        },

        onFileChange(e) {
            this.fotoBaru = e.target.files[0];
        },

        async updateProfil() {
            const formData = new FormData();

            formData.append("nama_lengkap", this.form.nama_lengkap ?? "");
            formData.append("email", this.form.email ?? "");
            formData.append("no_telepon", this.form.no_telepon ?? "");
            formData.append("nama_pengguna", this.form.nama_pengguna ?? "");

            if (this.form.kata_sandi && this.form.kata_sandi.length >= 6) {
                formData.append("kata_sandi", this.form.kata_sandi);
            }

            if (this.fotoBaru instanceof File) {
                formData.append("foto", this.fotoBaru);
            }

            formData.append("_method", "PUT");

            try {
                // ⬇️ SIMPAN RESPONSE
                const res = await axios.post(
                    `/api/pengguna/${this.idPengguna}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                const user = res.data.data;

                // 🔥 UPDATE localStorage (INI KUNCINYA)
                localStorage.setItem("nama", user.nama_lengkap);
                localStorage.setItem("foto", user.foto);

                // update tampilan profil
                this.form.foto = user.foto;

                this.showToast("Data berhasil diperbarui", "success");
                this.originalForm = JSON.parse(
                    JSON.stringify({
                        ...this.form,
                        kata_sandi: "",
                    })
                );
                this.fotoBaru = null;
                this.getProfil();
            } catch (error) {
                if (error.response?.status === 422) {
                    let pesan = "";
                    const errors = error.response.data.errors;
                    for (const key in errors) {
                        pesan += errors[key][0] + "\n";
                    }
                    this.showToast(pesan);
                } else {
                    this.showToast("Gagal memperbarui profil", "error");
                }
            }
        },
    },
};
</script>

<style scoped>
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    padding: 10px;
    border-radius: 6px;
}
</style>
