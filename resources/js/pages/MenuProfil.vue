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
                        class="w-full h-[320px] bg-gray-200 border-2 border-gray-300 rounded-lg flex items-center justify-center mb-4"
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
                            class="w-full bg-[#1E5AA8] text-white hover:bg-[#164B8A] py-3 rounded transition"
                        >
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

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

    methods: {
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
            } catch (error) {
                alert("Gagal mengambil data profil");
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

                alert("Data berhasil diperbarui");
                this.getProfil();
            } catch (error) {
                if (error.response?.status === 422) {
                    let pesan = "";
                    const errors = error.response.data.errors;
                    for (const key in errors) {
                        pesan += errors[key][0] + "\n";
                    }
                    alert(pesan);
                } else {
                    alert("Gagal memperbarui profil");
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
