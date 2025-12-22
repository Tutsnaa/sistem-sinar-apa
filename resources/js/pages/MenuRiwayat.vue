<template>
    <div class="min-h-screen bg-gray-100">
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

            <h1 class="text-xl font-semibold">RIWAYAT</h1>

            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- Content -->
        <div class="pt-32 px-6">
            <!-- Filter -->
            <div class="bg-white rounded shadow p-4 mb-6 flex flex-wrap gap-4">
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

                <!-- <div class="flex items-end">
                    <button
                        class="bg-[#3674B5] text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                    >
                        Tampilkan
                    </button>
                </div> -->
                <button
                    @click="downloadExcel"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Unduh Excel
                </button>
            </div>

            <!-- Tabel -->
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-lg font-bold mb-4">Daftar Riwayat Penjualan</h2>

                <div class="max-h-[500px] overflow-y-auto border rounded">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100 sticky top-0 z-20">
                            <tr>
                                <th class="border px-3 py-2 text-center">No</th>
                                <th class="border px-3 py-2 text-center">
                                    Tanggal
                                </th>
                                <th class="border px-3 py-2 text-center">
                                    Pengguna
                                </th>
                                <th class="border px-3 py-2 text-center">
                                    Total Penjualan
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="(item, index) in riwayatFiltered"
                                :key="item.id"
                            >
                                <td class="border px-3 py-2 text-center">
                                    {{ index + 1 }}
                                </td>

                                <td class="border px-3 py-2 text-center">
                                    {{ formatTanggal(item.created_at) }}
                                </td>

                                <td class="border px-3 py-2">
                                    {{ item.pengguna?.nama_lengkap }}
                                </td>

                                <td
                                    class="border px-3 py-2 text-right font-semibold"
                                >
                                    {{ formatRupiah(item.total) }}
                                </td>
                            </tr>

                            <tr v-if="riwayatFiltered.length === 0">
                                <td
                                    colspan="4"
                                    class="text-center py-4 text-gray-500"
                                >
                                    Tidak ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";

import axios from "axios";

export default {
    data() {
        return {
            nama: "",
            foto: "",
            periodeAwal: "",
            periodeAkhir: "",
            riwayat: [],
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.foto = foto;

        this.getRiwayat(); // load awal
    },

    computed: {
        riwayatFiltered() {
            return this.riwayat.filter((p) => {
                if (!this.periodeAwal && !this.periodeAkhir) return true;

                const tanggal = new Date(p.created_at).setHours(0, 0, 0, 0);

                const awal = this.periodeAwal
                    ? new Date(this.periodeAwal).setHours(0, 0, 0, 0)
                    : null;

                const akhir = this.periodeAkhir
                    ? new Date(this.periodeAkhir).setHours(23, 59, 59, 999)
                    : null;

                if (awal && tanggal < awal) return false;
                if (akhir && tanggal > akhir) return false;

                return true;
            });
        },
    },

    methods: {
        downloadExcel() {
            if (this.riwayatFiltered.length === 0) {
                alert("Tidak ada data untuk diunduh");
                return;
            }

            // Format periode
            const periode =
                this.periodeAwal && this.periodeAkhir
                    ? `${this.formatTanggal(
                          this.periodeAwal
                      )} s/d ${this.formatTanggal(this.periodeAkhir)}`
                    : "Semua Periode";

            // 🔹 Judul & keterangan
            const headerInfo = [
                ["LAPORAN PENJUALAN TOKO SINAR APA"],
                [`Periode : ${periode}`],
                [], // baris kosong
            ];

            // 🔹 Header tabel
            const tableHeader = [
                ["No", "Tanggal", "Pengguna", "Total Penjualan"],
            ];

            // 🔹 Isi data
            const tableBody = this.riwayatFiltered.map((item, index) => [
                index + 1,
                this.formatTanggal(item.created_at),
                item.pengguna?.nama_lengkap || "-",
                item.total,
            ]);

            // 🔹 Buat worksheet
            const worksheet = XLSX.utils.aoa_to_sheet([
                ...headerInfo,
                ...tableHeader,
                ...tableBody,
            ]);

            // 🔹 Lebar kolom
            worksheet["!cols"] = [
                { wch: 5 },
                { wch: 15 },
                { wch: 25 },
                { wch: 20 },
            ];

            // 🔹 Bold judul & header
            const range = XLSX.utils.decode_range(worksheet["!ref"]);
            for (let C = range.s.c; C <= range.e.c; ++C) {
                // Bold header (baris ke-4 karena 0-based, baris pertama data header)
                const cellRef = XLSX.utils.encode_cell({ r: 3, c: C });
                if (!worksheet[cellRef]) continue;
                worksheet[cellRef].s = { font: { bold: true } };
            }

            // 🔹 Format Rupiah (kolom D)
            const startRow = headerInfo.length + tableHeader.length + 1; // baris pertama data (1-based)
            for (let i = startRow; i <= startRow + tableBody.length - 1; i++) {
                const cellRef = `D${i}`;
                const cell = worksheet[cellRef];
                if (cell) {
                    cell.z = '"Rp "#,##0'; // Rupiah tanpa desimal
                }
            }

            // 🔹 Buat workbook & download
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Riwayat");

            const buffer = XLSX.write(workbook, {
                bookType: "xlsx",
                type: "array",
            });
            const blob = new Blob([buffer], {
                type: "application/octet-stream",
            });

            saveAs(blob, `Laporan_Riwayat_Penjualan.xlsx`);
        },

        getRiwayat() {
            axios
                .get("/api/penjualan")
                .then((res) => {
                    console.log("DATA PENJUALAN:", res.data.data);
                    this.riwayat = res.data.data ?? [];
                })
                .catch((err) => {
                    console.error(err);
                    alert("Gagal mengambil data penjualan");
                });
        },

        formatTanggal(tanggal) {
            return new Date(tanggal).toLocaleDateString("id-ID", {
                day: "2-digit",
                month: "short",
                year: "numeric",
            });
        },

        formatRupiah(angka) {
            return "Rp " + Number(angka || 0).toLocaleString("id-ID");
        },

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
    },
};
</script>
