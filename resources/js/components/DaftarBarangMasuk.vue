<template>
    <div class="bg-white rounded shadow p-4 mt-6">
        <!-- Judul -->
        <h2 class="text-lg font-bold mb-4">Daftar Barang Masuk</h2>
        <div class="flex items-end justify-between gap-4 mb-4 flex-wrap">
            <!-- Filter Status -->
            <div class="flex items-center gap-3">
                <label class="font-semibold whitespace-nowrap">
                    Filter Status:
                </label>
                <select v-model="filterStatus" class="border rounded px-3 py-1">
                    <option value="Semua">Semua</option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Diterima">Diterima</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
            </div>

            <!-- Periode -->
            <div class="flex items-end gap-3">
                <div>
                    <label class="block text-sm font-semibold mb-1">
                        Periode Awal
                    </label>
                    <input
                        type="date"
                        v-model="periodeAwal"
                        class="border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">
                        Periode Akhir
                    </label>
                    <input
                        type="date"
                        v-model="periodeAkhir"
                        class="border rounded px-3 py-2"
                    />
                </div>
            </div>
        </div>
        <!-- Tabel -->
        <div class="max-h-[500px] overflow-y-auto border rounded">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 sticky top-0 z-20">
                    <tr>
                        <th class="border px-3 py-2 text-center">No</th>
                        <th class="border px-3 py-2 text-center">
                            Nama Barang
                        </th>
                        <th class="border px-3 py-2 text-center">Jumlah</th>
                        <th class="border px-3 py-2 text-center">Harga Beli</th>
                        <th class="border px-3 py-2 text-center">Harga Jual</th>
                        <th class="border px-3 py-2 text-center">Total</th>
                        <th class="border px-3 py-2 text-center">Status</th>
                        <th class="border px-3 py-2 text-center">Tanggal</th>
                        <th class="border px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="(item, index) in barangMasukFiltered"
                        :key="item.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="border px-3 py-2 text-center">
                            {{ index + 1 }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ item.barang?.nama_barang || "-" }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ item.jumlah }}
                        </td>

                        <td class="border px-3 py-2 text-right">
                            {{ formatRupiah(item.harga_beli) }}
                        </td>

                        <td class="border px-3 py-2 text-right">
                            {{ formatRupiah(item.harga_jual) }}
                        </td>

                        <td class="border px-3 py-2 text-right font-semibold">
                            {{ formatRupiah(item.jumlah * item.harga_beli) }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="
                                    item.status === 'Diterima'
                                        ? 'bg-green-100 text-green-700'
                                        : item.status === 'Ditolak'
                                        ? 'bg-red-100 text-red-700'
                                        : item.status === 'Menunggu'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-gray-100 text-gray-700'
                                "
                            >
                                {{ item.status }}
                            </span>
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ formatTanggal(item.created_at) }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                <!-- Untuk karyawan -->
                                <template v-if="role === 'karyawan'">
                                    <button
                                        v-if="item.status !== 'Diterima'"
                                        @click="$emit('edit', item)"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition-colors"
                                    >
                                        Ubah
                                    </button>
                                    <button
                                        v-if="item.status !== 'Diterima'"
                                        @click="$emit('delete', item)"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                    >
                                        Hapus
                                    </button>

                                    <span
                                        v-if="item.status === 'Diterima'"
                                        class="text-gray-500 italic"
                                    >
                                        Terkunci
                                    </span>
                                </template>

                                <!-- Untuk pemilik_toko -->
                                <template v-else-if="role === 'pemilik_toko'">
                                    <button
                                        v-if="item.status === 'Menunggu'"
                                        @click="
                                            $emit('update-status', {
                                                item,
                                                status: 'Diterima',
                                            })
                                        "
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition-colors"
                                    >
                                        Terima
                                    </button>
                                    <button
                                        v-if="item.status === 'Menunggu'"
                                        @click="
                                            $emit('update-status', {
                                                item,
                                                status: 'Ditolak',
                                            })
                                        "
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors"
                                    >
                                        Tolak
                                    </button>
                                    <span
                                        v-if="item.status !== 'Menunggu'"
                                        class="text-gray-500 italic"
                                    >
                                        Terkunci
                                    </span>
                                </template>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="barangMasukFiltered.length === 0">
                        <td colspan="9" class="text-center py-4 text-gray-500">
                            Tidak ada data sesuai filter
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        barangMasuk: {
            type: Array,
            default: () => [],
        },
        role: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            filterStatus: "Semua",
            periodeAwal: "",
            periodeAkhir: "",
        };
    },

    mounted() {
        console.log("Barang Masuk dari parent:", this.barangMasuk);
    },

    methods: {
        formatRupiah(angka) {
            return "Rp " + Number(angka || 0).toLocaleString("id-ID");
        },

        formatTanggal(tanggal) {
            return new Date(tanggal).toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
            });
        },
    },
    computed: {
        barangMasukFiltered() {
            const now = new Date();
            const bulanSekarang = now.getMonth();
            const tahunSekarang = now.getFullYear();

            const isFilterTanggalAktif =
                this.periodeAwal !== "" || this.periodeAkhir !== "";

            return this.barangMasuk.filter((item) => {
                if (!item.created_at) return false;

                const tanggalItem = new Date(item.created_at);

                if (
                    this.filterStatus !== "Semua" &&
                    item.status !== this.filterStatus
                ) {
                    return false;
                }

                // DEFAULT: BULAN SEKARANG
                if (!isFilterTanggalAktif) {
                    return (
                        tanggalItem.getMonth() === bulanSekarang &&
                        tanggalItem.getFullYear() === tahunSekarang
                    );
                }

                // FILTER MANUAL
                const awal = this.periodeAwal
                    ? new Date(this.periodeAwal + "T00:00:00")
                    : null;

                const akhir = this.periodeAkhir
                    ? new Date(this.periodeAkhir + "T23:59:59")
                    : null;

                if (awal && tanggalItem < awal) return false;
                if (akhir && tanggalItem > akhir) return false;

                return true;
            });
        },
    },
};
</script>
