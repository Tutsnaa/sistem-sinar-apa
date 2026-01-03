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

                <!-- Satuan -->
                <div>
                    <label class="block font-medium mb-1">Satuan</label>
                    <select
                        v-model="form.satuan"
                        placeholder="Satuan"
                        class="w-full border rounded px-3 py-2"
                        required
                    >
                        <option value="">-- Pilih Satuan --</option>
                        <option v-for="s in satuanList" :key="s" :value="s">
                            {{ s }}
                        </option>
                    </select>
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
                        class="px-4 py-2 border rounded bg-gray-300 hover:bg-gray-400"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
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
        barang: Object, // ⬅ data barang yang mau diubah
    },

    data() {
        return {
            satuanList: ["Pcs", "Unit", "Set", "Box", "Pack", "Roll", "Meter"],
            form: {
                nama_barang: "",
                satuan: "",
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
            satuan: this.barang.satuan,
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
