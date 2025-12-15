<template>
    <div class="p-6 bg-white rounded-lg shadow-md w-full max-w-auto">
        <h2 class="text-xl font-bold mb-4">Daftar Pembelian</h2>

        <!-- Tabel pembelian -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border border-gray-300 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">No</th>
                        <th class="px-4 py-2 border-b">Nama Barang</th>
                        <th class="px-4 py-2 border-b">Jumlah</th>
                        <th class="px-4 py-2 border-b">Harga</th>
                        <th class="px-4 py-2 border-b">Total Harga</th>
                        <th class="px-4 py-2 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in daftarPembelian"
                        :key="index"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2">{{ item.nama }}</td>
                        <td class="px-4 py-2">{{ item.jumlah }}</td>
                        <td class="px-4 py-2">
                            {{ formatRupiah(item.harga) }}
                        </td>
                        <td class="px-4 py-2">
                            {{ formatRupiah(item.harga * item.jumlah) }}
                        </td>

                        <td class="px-4 py-2">
                            <button
                                @click="hapusItem(index)"
                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <tr v-if="daftarPembelian.length === 0">
                        <td class="px-4 py-2 text-center" colspan="6">
                            Belum ada pembelian
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total, Bayar, Kembalian, Tombol -->
        <div class="flex flex-col md:flex-row justify-between mt-4 gap-4">
            <!-- Total -->
            <div class="flex items-center space-x-2">
                <span class="font-bold">Total:</span>
                <span>{{ formatRupiah(totalHarga) }}</span>
            </div>

            <!-- Bayar dan Kembalian -->
            <div class="flex flex-col md:flex-row items-end gap-2">
                <div class="flex flex-col">
                    <label class="font-bold">Bayar</label>
                    <input
                        type="text"
                        :value="formatRupiah(bayar)"
                        @input="onInputBayar"
                        class="border px-2 py-1 rounded w-32"
                        placeholder="Jumlah bayar"
                    />
                </div>
                <div class="flex flex-col">
                    <label class="font-bold">Kembalian</label>
                    <input
                        type="text"
                        :value="formatRupiah(kembalian)"
                        class="border px-2 py-1 rounded w-32 bg-gray-100"
                        readonly
                    />
                </div>
                <!-- Tombol -->
                <button
                    @click="batal"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                >
                    Batal
                </button>
                <button
                    @click="simpan"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    emits: ["simpan-penjualan"],
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
