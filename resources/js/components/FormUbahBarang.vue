<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Ubah Barang</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-red-500"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="updateBarang" class="space-y-4">
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
                        class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600"
                    >
                        Update
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
        barang: Object, // ⬅ data barang yang mau diubah
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

    mounted() {
        // isi form otomatis
        this.form = {
            nama_barang: this.barang.nama_barang,
            id_kategori: this.barang.id_kategori,
            harga_beli: this.barang.harga_beli,
            harga_jual: this.barang.harga_jual,
            jumlah: this.barang.jumlah,
        };
    },

    methods: {
        async updateBarang() {
            try {
                await axios.put(`/api/barang/${this.barang.id}`, this.form);

                this.$emit("success");
                this.$emit("close");
            } catch (e) {
                alert("Gagal mengubah barang");
            }
        },

        formatRupiah(angka) {
            if (!angka) return "";
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        unformatRupiah(angka) {
            return angka.replace(/\./g, "");
        },
    },
};
</script>
