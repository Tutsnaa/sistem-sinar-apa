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
            class="flex flex-col md:flex-row justify-between gap-4 mt-auto pt-4"
        >
            <!-- Total -->
            <div class="flex items-center gap-2">
                <span class="font-bold">Total:</span>
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
    </div>
</template>

<script>
export default {
    emits: ["simpan-penjualan", "edit"],
    name: "DaftarPembelian",
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
        selesaiEdit() {
            this.editIndex = null;
            this.bayar = 0; // ⬅️ WAJIB RESET
            console.log(this.daftarPembelian);
        },
        ubahItem(item) {
            this.$emit("edit", item);
            console.log(item);
        },
        hapusItem(index) {
            this.daftarPembelian.splice(index, 1);
        },
        batal() {
            this.daftarPembelian.splice(0);
            this.bayar = 0;
        },

        simpan() {
            if (this.bayar < this.totalHarga) {
                alert("Jumlah bayar kurang!");
                return;
            }

            this.$emit("simpan-penjualan", {
                bayar: this.bayar,
            });
        },
        /*************  ✨ Windsurf Command ⭐  *************/
        /**
         * Konversi string "Rp 15.000" ke number 15000
         * @param {string} hargaStr
         * @returns {number}
         */
        /*******  e05a46b6-637c-4360-93aa-b733c3a0bcc3  *******/

        onInputBayar(e) {
            // Ambil angka saja (hapus Rp, titik, spasi)
            const raw = e.target.value.replace(/[^0-9]/g, "");
            this.bayar = raw ? Number(raw) : 0;
        },

        // ✅ FORMAT SESUAI PERMINTAAN
        formatRupiah(number) {
            if (!number) return "Rp 0";
            return "Rp " + Number(number).toLocaleString("id-ID");
        },
    },
};
</script>
