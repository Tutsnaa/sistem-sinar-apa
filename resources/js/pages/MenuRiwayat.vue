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
        <div class="pt-28 px-6">
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
                    class="bg-[#3674B5] text-white hover:bg-sky-600 px-4 py-2 rounded"
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
                                <th class="border px-3 py-2 text-center">
                                    Keuntungan
                                </th>
                                <th class="border px-3 py-2 text-center">
                                    Aksi
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
                                <td
                                    class="border px-3 py-2 text-right font-semibold"
                                >
                                    {{ formatRupiah(item.keuntungan) }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <button
                                        @click="lihatInvoice(item.id)"
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                                    >
                                        Invoice
                                    </button>
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
    <!-- POPUP INVOICE -->
    <div
        v-if="showInvoice"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white w-[400px] rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-2 text-center">INVOICE</h2>

            <p><b>ID:</b> {{ invoiceData.id_penjualan }}</p>
            <p><b>Tanggal:</b> {{ invoiceData.tanggal }}</p>
            <p><b>Kasir:</b> {{ invoiceData.kasir }}</p>
            <p><b>Pelanggan:</b> {{ invoiceData.pelanggan }}</p>

            <hr class="my-3" />

            <div
                v-for="item in invoiceData.items"
                :key="item.id_barang"
                class="flex justify-between text-sm"
            >
                <span>{{ item.nama }} x{{ item.jumlah }}</span>
                <span>Rp {{ item.harga * item.jumlah }}</span>
            </div>

            <hr class="my-3" />

            <p><b>Total:</b> Rp {{ invoiceData.total }}</p>
            <p><b>Bayar:</b> Rp {{ invoiceData.bayar }}</p>
            <p><b>Kembalian:</b> Rp {{ invoiceData.kembalian }}</p>

            <div class="flex justify-end gap-3 mt-4">
                <button
                    @click="downloadInvoice"
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Download
                </button>

                <button
                    @click="showInvoice = false"
                    class="bg-gray-300 px-4 py-2 rounded"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";
import { jsPDF } from "jspdf";

import axios from "axios";

export default {
    data() {
        return {
            nama: "",
            foto: "",
            periodeAwal: "",
            periodeAkhir: "",
            riwayat: [],
            showInvoice: false,
            invoiceData: null,
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
        async lihatInvoice(id) {
            try {
                const res = await axios.get(`/api/penjualan/${id}`);
                console.log("RES DATA:", res.data);
                const d = res.data.data;

                this.invoiceData = {
                    id_penjualan: d.id_penjualan,
                    tanggal: d.tanggal,
                    kasir: d.kasir,
                    pelanggan: d.pelanggan,
                    items: d.items,
                    total: parseFloat(d.total),
                    bayar: parseFloat(d.bayar),
                    kembalian: parseFloat(d.kembalian),
                };

                this.showInvoice = true;
            } catch (err) {
                console.error("Gagal ambil invoice:", err);
                alert("Gagal memuat invoice");
            }
        },

        downloadInvoice() {
            if (!this.invoiceData) return;

            const d = this.invoiceData;
            const lineHeight = 5;

            // Catatan invoice
            const noteLines = [
                "Barang yang sudah dibeli tidak dapat dikembalikan.",
                "Terima kasih telah berbelanja di Toko Sinar Apa!",
            ];

            // Hitung jumlah baris untuk menentukan tinggi kertas
            const numHeaderLines = 8; // judul, alamat, telp, garis, info invoice, pelanggan
            const numItemLines = d.items.length + 1; // header tabel + items
            const numSummaryLines = 3; // Total, Bayar, Kembalian
            const numNoteLines = noteLines.length + 1; // +1 untuk judul "Catatan:"
            const extraBottomMargin = 20; // Jarak ekstra di bawah agar lebih panjang
            const totalLines =
                numHeaderLines + numItemLines + numSummaryLines + numNoteLines;

            const pageHeight = totalLines * lineHeight + extraBottomMargin; // panjang kertas otomatis

            const doc = new jsPDF({
                unit: "mm",
                format: [80, pageHeight], // lebar 80mm, panjang sesuai isi + margin bawah
            });

            let y = 5; // posisi vertikal awal

            // Header Toko
            doc.setFont("helvetica", "bold");
            doc.setFontSize(10);
            doc.text("TOKO SINAR APA", 40, y, { align: "center" });
            y += lineHeight;

            doc.setFont("helvetica", "normal");
            doc.setFontSize(8);
            doc.text("Jl. Krisna, Mas, Kecamatan Ubud", 40, y, {
                align: "center",
            });
            y += lineHeight;
            doc.text("Telp: (0361) 123456 | 0812-3456-789", 40, y, {
                align: "center",
            });
            y += lineHeight;
            doc.line(5, y, 75, y);
            y += lineHeight;

            // Info Invoice
            doc.text("No.Invoice      :", 5, y);
            doc.text(String(d.id_penjualan), 28, y);
            y += lineHeight;
            doc.text("Tanggal          :", 5, y);
            doc.text(d.tanggal, 28, y);
            y += lineHeight;
            doc.text("Kasir               :", 5, y);
            doc.text(d.kasir, 28, y);
            y += lineHeight;

            // Info Pelanggan
            doc.text("Pelanggan      :", 5, y);
            doc.text(d.pelanggan, 28, y);
            y += lineHeight;

            doc.line(5, y, 75, y);
            y += lineHeight;

            // Header tabel
            doc.setFont("helvetica", "bold");
            doc.text("Nama", 5, y);
            doc.text("Jumlah", 35, y);
            doc.text("Harga", 50, y);
            doc.text("Total", 75, y, { align: "right" });
            y += lineHeight;
            doc.setFont("helvetica", "normal");

            // Fungsi format angka
            const formatNumber = (n) => Number(n).toLocaleString("id-ID");

            // Items
            d.items.forEach((item) => {
                doc.text(item.nama, 5, y);
                doc.text(String(item.jumlah), 35, y);
                doc.text(formatNumber(item.harga), 50, y);
                doc.text(formatNumber(item.harga * item.jumlah), 75, y, {
                    align: "right",
                });
                y += lineHeight;
            });

            doc.line(5, y, 75, y);
            y += lineHeight;

            const rightX = 75; // posisi angka paling kanan
            const labelX = 5;
            const labelWidth = 35;

            // Ringkasan pembayaran
            function writeLabelValue(label, value) {
                doc.text(label, labelX + labelWidth, y, { align: "left" });
                doc.text(formatNumber(value), rightX, y, { align: "right" });
                y += lineHeight;
            }

            writeLabelValue("Total         :", d.total);
            writeLabelValue("Bayar        :", d.bayar);
            writeLabelValue("Kembalian :", d.kembalian);

            doc.line(5, y, 75, y);
            y += lineHeight;

            // Catatan
            doc.setFontSize(7);
            doc.text("Catatan:", 5, y);
            y += lineHeight;
            noteLines.forEach((line) => {
                doc.text(line, 5, y);
                y += lineHeight;
            });

            // Margin bawah tambahan supaya catatan tidak menempel di batas kertas
            y += extraBottomMargin;

            doc.save(`Invoice_${d.id_penjualan}.pdf`);
        },

        downloadExcel() {
            if (this.riwayatFiltered.length === 0) {
                alert("Tidak ada data untuk diunduh");
                return;
            }

            // 🔹 Format periode
            const periode =
                this.periodeAwal && this.periodeAkhir
                    ? `${this.formatTanggal(
                          this.periodeAwal
                      )} s/d ${this.formatTanggal(this.periodeAkhir)}`
                    : "Semua Periode";

            // 🔹 Header laporan
            const headerInfo = [
                ["LAPORAN PENJUALAN TOKO SINAR APA"],
                [`Periode : ${periode}`],
                [],
            ];

            // 🔹 Header tabel
            const tableHeader = [
                ["No", "Tanggal", "Pengguna", "Total Penjualan", "Keuntungan"],
            ];

            // 🔹 Data tabel
            const tableBody = this.riwayatFiltered.map((item, index) => [
                index + 1,
                this.formatTanggal(item.created_at),
                item.pengguna?.nama_lengkap || "-",
                Number(item.total || 0),
                Number(item.keuntungan || 0),
            ]);

            // 🔹 Hitung TOTAL
            const totalPenjualan = this.riwayatFiltered.reduce(
                (sum, item) => sum + Number(item.total || 0),
                0
            );

            const totalKeuntungan = this.riwayatFiltered.reduce(
                (sum, item) => sum + Number(item.keuntungan || 0),
                0
            );

            // 🔹 Baris TOTAL
            const totalRow = ["", "", "TOTAL", totalPenjualan, totalKeuntungan];

            // 🔹 Gabungkan semua data
            const worksheet = XLSX.utils.aoa_to_sheet([
                ...headerInfo,
                ...tableHeader,
                ...tableBody,
                [], // baris kosong
                totalRow,
            ]);

            // 🔹 Lebar kolom
            worksheet["!cols"] = [
                { wch: 5 },
                { wch: 15 },
                { wch: 25 },
                { wch: 20 },
                { wch: 20 },
            ];

            // 🔹 Bold header tabel
            const range = XLSX.utils.decode_range(worksheet["!ref"]);
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cellRef = XLSX.utils.encode_cell({ r: 3, c: C });
                if (worksheet[cellRef]) {
                    worksheet[cellRef].s = { font: { bold: true } };
                }
            }

            // 🔹 Format Rupiah kolom D & E
            const startRow = headerInfo.length + tableHeader.length + 1;
            const endRow = startRow + tableBody.length;

            ["D", "E"].forEach((col) => {
                for (let i = startRow; i <= endRow + 1; i++) {
                    const cell = worksheet[`${col}${i}`];
                    if (cell) {
                        cell.z = "#,##0";
                        cell.t = "n";
                    }
                }
            });

            // 🔹 Bold baris TOTAL
            const totalRowIndex = endRow + 2;
            ["C", "D", "E"].forEach((col) => {
                const cell = worksheet[`${col}${totalRowIndex}`];
                if (cell) {
                    cell.s = { font: { bold: true } };
                }
            });

            // 🔹 Buat file Excel
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Riwayat");

            const buffer = XLSX.write(workbook, {
                bookType: "xlsx",
                type: "array",
            });

            const blob = new Blob([buffer], {
                type: "application/octet-stream",
            });

            saveAs(blob, "Laporan_Riwayat_Penjualan.xlsx");
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
