<template>
    <div
        class="min-h-[calc(78vh-80px)] p-6 bg-white rounded-lg shadow-md flex flex-col"
    >
        <!-- Judul -->
        <h2 class="text-xl font-bold mb-4">Daftar Pembelian</h2>

        <!-- TABEL -->
        <div
            class="min-h-[303px] max-h-[325px] overflow-y-auto border border-gray-300 rounded""
        >
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Nama Barang</th>
                        <th class="px-4 py-2 border">Jumlah</th>
                        <th class="px-4 py-2 border">Harga</th>
                        <th class="px-4 py-2 border">Total Harga</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in daftarPembelian"
                        :key="index"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="border px-4 py-2 text-center">
                            {{ index + 1 }}
                        </td>
                        <td class="border px-4 py-2">{{ item.nama }}</td>

                        <td class="border px-4 py-2 text-center">
                            <span v-if="editIndex !== index">
                                {{ item.jumlah }}
                            </span>
                            <input
                                v-else
                                type="number"
                                min="1"
                                v-model.number="item.jumlah"
                                class="border px-2 py-1 rounded w-20"
                            />
                        </td>

                        <td class="border px-4 py-2">
                            {{ formatRupiah(item.harga) }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ formatRupiah(item.harga * item.jumlah) }}
                        </td>

                        <td class="px-4 py-2 flex justify-center gap-2">
                            <button
                                v-if="editIndex !== index"
                                @click="editIndex = index"
                                class="bg-yellow-500 text-white hover:bg-yellow-600 px-2 py-1 rounded"
                            >
                                Ubah
                            </button>

                            <button
                                v-else
                                @click="selesaiEdit"
                                class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-2 py-1 rounded"
                            >
                                Simpan
                            </button>

                            <button
                                @click="hapusItem(index)"
                                class="bg-[#DC3545] text-white hover:bg-[#B02A37] px-2 py-1 rounded"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <tr v-if="daftarPembelian.length === 0">
                        <td colspan="6" class="px-4 py-4 text-center">
                            Belum ada pembelian
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- BLOK BAWAH (NEMPEL BAWAH) -->
        <div
            class="flex flex-col md:flex-row justify-between gap-2 mt-auto pt-4"
        >
            <!-- Total -->
            <div class="flex items-center gap-2">
                <span class="font-bold">Subtotal:</span>
                <span
                    class="flex items-center text-xl border px-2 py-1 rounded w-60 h-20"
                >
                    {{ formatRupiah(totalHarga) }}
                </span>
            </div>

            <!-- Bayar -->
            <div class="flex items-center gap-2">
                <span class="font-bold">Bayar:</span>
                <input
                    type="text"
                    :value="formatRupiah(bayar)"
                    @keydown="onlyNumber"
                    @input="onInputBayar"
                    class="flex items-center text-xl border px-2 py-1 rounded w-60 h-20"
                    placeholder="Jumlah bayar"
                />
            </div>

            <!-- Kembalian -->
            <div class="flex items-center gap-2">
                <span class="font-bold">Kembalian:</span>
                <input
                    type="text"
                    :value="formatRupiah(kembalian)"
                    class="flex items-center text-xl border px-2 py-1 rounded w-60 h-20 bg-gray-100"
                    readonly
                />
            </div>

            <!-- Tombol -->
            <div class="flex flex-col md:flex-row items-end gap-2">
                <button
                    @click="batal"
                    class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
                >
                    Batal
                </button>
                <button
                    @click="simpan"
                    class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
                >
                    Simpan
                </button>
            </div>
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
export default {
    name: "DaftarPembelian",
    emits: ["simpan-penjualan", "edit"],
    props: {
        daftarPembelian: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            bayar: 0,
            editIndex: null,
            toast: {
                show: false,
                message: "",
                type: "success", // success | error
            },
        };
    },

    computed: {
        totalHarga() {
            return this.daftarPembelian.reduce(
                (total, item) => total + item.harga * item.jumlah,
                0
            );
        },

        kembalian() {
            return this.bayar - this.totalHarga;
        },
    },

    methods: {
        // Menampilkan notifikasi toast
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

        // Membatasi input hanya angka
        onlyNumber(e) {
            if (
                !/[0-9]/.test(e.key) &&
                !["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab"].includes(e.key)
            ) {
                e.preventDefault();
            }
        },

        // Selesai edit item
        selesaiEdit() {
            this.editIndex = null;
            this.bayar = 0; // Reset input bayar
            console.log(this.daftarPembelian);
        },

        // Emit perubahan item
        ubahItem(item) {
            this.$emit("edit", item);
            console.log(item);
        },

        // Hapus item dari daftar pembelian
        hapusItem(index) {
            this.daftarPembelian.splice(index, 1);
        },

        // Membatalkan transaksi, mengosongkan daftar dan input bayar
        batal() {
            this.bayar = 0;
        },

        // Simpan transaksi penjualan
        simpan() {
            if (this.bayar < this.totalHarga) {
                this.showToast("Jumlah bayar kurang!", "error");
                return;
            }

            this.$emit("simpan-penjualan", {
                bayar: this.bayar,
            });

            // Tampilkan notifikasi sukses
            // this.showToast("Transaksi berhasil disimpan!", "success");

            // Reset form
            
            this.bayar = 0;
        },

        // Ambil input bayar dan konversi ke number
        onInputBayar(e) {
            const raw = e.target.value.replace(/[^0-9]/g, "");
            this.bayar = raw ? Number(raw) : 0;
        },

        // Format angka menjadi rupiah
        formatRupiah(number) {
            if (!number) return "Rp 0";
            return "Rp " + Number(number).toLocaleString("id-ID");
        },
    },
};
</script>

