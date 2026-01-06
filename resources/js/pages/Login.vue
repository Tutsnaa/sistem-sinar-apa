<template>
    <div class="min-h-screen flex">
        <!-- Bagian Kiri -->
        <div
            class="w-1/2 bg-[#3674B5] flex flex-col items-center justify-center text-center p-8"
        >
            <div
                class="w-52 h-52 bg-white rounded-full flex items-center justify-center mb-6 shadow-md"
            >
                <img :src="logo" alt="Logo Toko" class="w-32 h-32" />
            </div>
            <h1 class="text-4xl font-bold text-white tracking-wide text-center">
                TOKO SINAR APA
            </h1>
        </div>

        <!-- Bagian Kanan -->
        <div class="w-1/2 flex items-center justify-center bg-white p-8">
            <div class="w-full max-w-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    Selamat Datang!
                </h2>

                <form @submit.prevent="login">
                    <FormInput
                        id="username"
                        label="Nama Pengguna"
                        placeholder="Masukkan Nama Pengguna"
                        v-model="namapengguna"
                        @update:modelValue="errorNama = ''"
                        :error="errorNama"
                        @focus="errorNama = ''"
                    />
                    <FormInputPassword
                        id="password"
                        v-model="katasandi"
                        label="Kata Sandi"
                        placeholder="Masukkan Kata Sandi"
                        :error="errorSandi"
                        @input="errorSandi = ''"
                    />

                    <ButtonPrimary type="submit" :disabled="loading">
                        {{ loading ? "Memproses..." : "MASUK" }}
                    </ButtonPrimary>
                </form>
            </div>
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
import ButtonPrimary from "../components/ButtonPrimary.vue";
import FormInput from "../components/FormInput.vue";
import FormInputPassword from "../components/FormInputPassword.vue";

export default {
    name: "Login",
    components: { ButtonPrimary, FormInput, FormInputPassword },

    data() {
        return {
            namapengguna: "",
            katasandi: "",
            logo: "/assets/img/Logo-Toko.png",
            loading: false,
            toast: "",
            errorNama: "",
            errorSandi: "",
        };
    },

    methods: {
        showToast(pesan) {
            this.toast = pesan;
            setTimeout(() => {
                this.toast = "";
            }, 3000);
        },

        async login() {
            this.errorNama = "";
            this.errorSandi = "";

            if (!this.namapengguna) {
                this.errorNama = "Nama pengguna wajib diisi";
                return;
            }

            if (!this.katasandi) {
                this.errorSandi = "Kata sandi wajib diisi";
                return;
            }

            this.loading = true;

            try {
                const response = await axios.post("/api/login", {
                    nama_pengguna: this.namapengguna,
                    kata_sandi: this.katasandi,
                });

                if (!response.data.success) {
                    this.showToast(
                        response.data.message ||
                            "Nama pengguna atau kata sandi salah"
                    );
                    return;
                }

                const user = response.data.data;

                // 🔐 Simpan data user
                localStorage.setItem("id_pengguna", user.id);
                localStorage.setItem("role", user.role);
                localStorage.setItem("nama", user.nama);
                localStorage.setItem("foto", user.foto ?? "");

                sessionStorage.setItem("toast_success", "Login berhasil");

                // 🔁 Redirect sesuai role
                if (user.role === "pemilik_toko") {
                    this.$router.replace("/beranda-pemilik");
                } else if (user.role === "karyawan") {
                    this.$router.replace("/beranda-karyawan");
                } else {
                    alert("Role tidak dikenali");
                    localStorage.clear();
                }
            } catch (error) {
                if (error.response) {
                    // Pesan dari Laravel
                    this.showToast(error.response.data.message);
                } else {
                    // Error jaringan / server mati
                    this.showToast("Terjadi kesalahan server");
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
