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
                            @keypress="onlyNumber"
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
                            @keypress="onlyNumber"
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
        <!-- NOTIFICATION -->
        <div
            v-if="toast.show"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down text-white"
            :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
        >
            <!-- ICON -->
            <!-- SUCCESS -->
            <svg
                v-if="toast.type === 'success'"
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

            <!-- ERROR -->
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
                {{ toast.message }}
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
            toast: {
                show: false,
                message: "",
                type: "success", // success | error
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
        onlyNumber(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
            }
        },

        showToast(message, type = "success") {
            this.toast = {
                show: true,
                message,
                type,
            };

            setTimeout(() => {
                this.toast.show = false;
            }, 2000);
        },

        async updateBarang() {
            try {
                await axios.put(`/api/barang/${this.barang.id}`, this.form);

                this.showToast("Barang berhasil diperbarui", "success");
                this.$emit("success");
                setTimeout(() => {
                    this.$emit("close");
                }, 2000); // tunggu toast selesai
            } catch (e) {
                this.showToast("Gagal mengubah barang", "error");
            }
        },

        formatRupiah(value) {
            if (value === "" || value === null || isNaN(value)) return "";
            return new Intl.NumberFormat("id-ID").format(value);
        },

        unformatRupiah(value) {
            // hapus semua selain angka
            const angka = value.replace(/[^\d]/g, "");
            return angka === "" ? "" : Number(angka);
        },
    },
};
</script>
