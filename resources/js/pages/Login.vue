<template>
    <div class="min-h-screen flex">
        <!-- Bagian Kiri -->
        <div
            class="w-1/2 bg-[#3674B5] flex flex-col items-center justify-center text-center p-8"
        >
            <div
                class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 shadow-md"
            >
                <img :src="logo" alt="Logo Toko" class="w-24 h-24" />
            </div>
            <h1 class="text-4xl font-bold text-white tracking-wide text-center">
                SISTEM INFORMASI PENJUALAN DAN <br />PERSEDIAAN TOKO SINAR APA
            </h1>
        </div>

        <!-- Bagian Kanan -->
        <div class="w-1/2 flex items-center justify-center bg-white p-8">
            <div class="w-full max-w-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    Selamat Datang Kembali!
                </h2>

                <form @submit.prevent="login">
                    <FormInput
                        id="username"
                        label="Nama Pengguna"
                        placeholder="Masukkan Nama Pengguna"
                        v-model="namapengguna"
                    />
                    <FormInputPassword
                        id="password"
                        v-model="katasandi"
                        label="Kata Sandi"
                        placeholder="Masukkan Kata Sandi"
                    />

                    <ButtonPrimary @click="login">MASUK</ButtonPrimary>
                </form>
            </div>
        </div>
    </div>
</template>

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
        };
    },
    methods: {
        async login() {
            if (!this.namapengguna || !this.katasandi) {
                alert("Nama pengguna dan kata sandi wajib diisi!");
                return;
            }

            try {
                const response = await axios.post("/login", {
                    nama_pengguna: this.namapengguna,
                    kata_sandi: this.katasandi,
                });

                if (response.data.success) {
                    // simpan role di localStorage agar dashboard bisa membaca
                    localStorage.setItem("role", response.data.role);

                    // redirect ke dashboard sesuai role
                    if (response.data.role === "pemilik_toko") {
                        this.$router.push("/dashboard-pemilik");
                    } else if (response.data.role === "karyawan") {
                        this.$router.push("/dashboard-karyawan");
                    }
                }
            } catch (error) {
                const pesan = error.response?.data?.message || "Login gagal!";
                alert(pesan);
            }
        },
    },
};
</script>
