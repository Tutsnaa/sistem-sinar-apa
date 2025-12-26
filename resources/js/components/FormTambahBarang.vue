<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Tambah Barang</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-red-500"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="simpanBarang" class="space-y-4">
                <!-- Nama Barang -->
                <div>
                    <label class="block font-medium mb-1">Nama Barang</label>
                    <input
                        v-model="form.nama_barang"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        required
                    />
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block font-medium mb-1">Kategori</label>
                    <select
                        v-model="form.id_kategori"
                        class="w-full border rounded px-3 py-2"
                        required
                    >
                        <option value="">-- Pilih Kategori --</option>
                        <option v-for="k in kategori" :key="k.id" :value="k.id">
                            {{ k.nama_kategori }}
                        </option>
                    </select>
                </div>

                <!-- Harga -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium mb-1">Harga Beli</label>
                        <input
                            type="text"
                            :value="formatRupiah(form.harga_beli)"
                            @keydown="onlyNumber"
                            @input="
                                form.harga_beli = unformatRupiah(
                                    $event.target.value
                                )
                            "
                            class="w-full border rounded px-3 py-2"
                            required
                        />
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Harga Jual</label>
                        <input
                            type="text"
                            :value="formatRupiah(form.harga_jual)"
                            @keydown="onlyNumber"
                            @input="
                                form.harga_jual = unformatRupiah(
                                    $event.target.value
                                )
                            "
                            class="w-full border rounded px-3 py-2"
                            required
                        />
                    </div>
                </div>

                <!-- Jumlah -->
                <div>
                    <label class="block font-medium mb-1">Jumlah</label>
                    <input
                        v-model="form.jumlah"
                        type="number"
                        class="w-full border rounded px-3 py-2"
                        required
                    />
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-2 pt-4">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 border rounded"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        kategori: Array,
    },
    data() {
        return {
            form: {
                nama_barang: "",
                id_kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: "",
            },
        };
    },
    methods: {
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
        async simpanBarang() {
            try {
                await axios.post("/api/barang", this.form);
                this.$emit("success");
                this.$emit("close");
            } catch (e) {
                alert("Gagal menyimpan barang");
            }
        },

        formatRupiah(value) {
            if (value === null || value === undefined || value === "")
                return "";

            return Number(value).toLocaleString("id-ID");
        },

        unformatRupiah(value) {
            if (!value) return 0;

            // hapus titik, koma, dan spasi
            return Number(value.replace(/[^\d]/g, ""));
        },
    },
};
</script>
