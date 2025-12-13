<template>
    <div class="mb-4">
        <!-- Judul Form -->
        <h2 class="text-xl font-bold mb-4 mt-2">Tambah Kategori</h2>

        <!-- Input & Tombol -->
        <div class="flex items-center gap-2">
            <input
                type="text"
                v-model="namaKategori"
                placeholder="Nama kategori"
                class="border border-gray-300 rounded px-3 py-2 w-64"
            />
            <button
                @click="tambahKategori"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
            >
                Simpan
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormTambahKategori",
    data() {
        return {
            namaKategori: "",
        };
    },
    methods: {
        tambahKategori() {
            if (!this.namaKategori) {
                alert("Nama kategori wajib diisi!");
                return;
            }

            this.$emit("tambah-kategori", {
                nama_kategori: this.namaKategori,
            });

            this.namaKategori = "";
        },
    },
};
</script>
