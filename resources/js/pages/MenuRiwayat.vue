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

                <button
                    @click="downloadExcel"
                    class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
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
                                        class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-3 py-1 rounded"
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
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
        <div class="bg-white shadow-lg flex">
            <!-- INVOICE -->
            <div class="p-4">
                <div class="w-[290px] border text-black font-mono p-3">
                    <!-- HEADER -->
                    <div class="text-center p-5">
                        <div class="font-bold text-sm">TOKO SINAR APA</div>
                        <div class="text-[10px] leading-tight">
                            Jl. Krisna, Mas, Kecamatan Ubud
                        </div>
                        <div class="text-[10px] pt-1 leading-tight">
                            Telp: (0361) 123456 | 0812-3456-789
                        </div>
                    </div>
                    <hr class="border-black border-dashed w-[95%] mx-auto" />

                    <hr
                        class="border-black border-dashed my-1 w-[95%] mx-auto"
                    />

                    <!-- INFO -->
                    <div class="text-[11px] p-2 space-y-0.5">
                        <div class="flex">
                            <span class="w-20">No.Invoice</span>
                            <span>: {{ invoiceData.id_penjualan }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-20">Tanggal</span>
                            <span>: {{ invoiceData.tanggal }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-20">Kasir</span>
                            <span>: {{ invoiceData.kasir }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-20">Pelanggan</span>
                            <span>: {{ invoiceData.pelanggan }}</span>
                        </div>
                    </div>

                    <hr class="border-black border-dashed w-[95%] mx-auto" />

                    <!-- ITEM HEADER -->
                    <div class="text-[11px] p-2">
                        <div
                            class="grid grid-cols-[minmax(0,1fr)_40px_55px_55px] font-bold"
                        >
                            <div>Nama</div>
                            <div class="text-right">Jumlah</div>
                            <div class="text-right">Harga</div>
                            <div class="text-right">Total</div>
                        </div>
                    </div>

                    <!-- ITEM DATA -->
                    <div class="text-[11px] px-2 space-y-0.5">
                        <div
                            v-for="item in invoiceData.items"
                            :key="item.id_barang"
                            class="grid grid-cols-[minmax(0,1fr)_40px_55px_55px]"
                        >
                            <!-- NAMA BARANG -->
                            <div
                                class="whitespace-normal wrap-break-word leading-tight text-wrap"
                            >
                                {{ item.nama }}
                            </div>

                            <!-- QTY -->
                            <div class="text-center">
                                {{ item.jumlah }}
                            </div>

                            <!-- HARGA -->
                            <div class="text-right">
                                {{ Number(item.harga).toLocaleString("id-ID") }}
                            </div>

                            <!-- TOTAL -->
                            <div class="text-right font-medium">
                                {{
                                    (item.harga * item.jumlah).toLocaleString(
                                        "id-ID"
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <hr
                        class="border-black border-dashed my-1 w-[95%] mx-auto"
                    />

                    <!-- TOTAL -->
                    <div class="text-[11px] p-2 space-y-0.5">
                        <div class="flex">
                            <span class="w-20">Subtotal</span>

                            <span class="ml-auto text-right w-[70px]">
                                {{ invoiceData.total.toLocaleString("id-ID") }}
                            </span>
                        </div>

                        <div class="flex">
                            <span class="w-20">Bayar</span>

                            <span class="ml-auto text-right w-[70px]">
                                {{ invoiceData.bayar.toLocaleString("id-ID") }}
                            </span>
                        </div>

                        <div class="flex font-bold">
                            <span class="w-20">Kembalian</span>

                            <span class="ml-auto text-right w-[70px]">
                                {{
                                    invoiceData.kembalian.toLocaleString(
                                        "id-ID"
                                    )
                                }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-black border-dashed w-[95%] mx-auto" />

                    <!-- FOOTER -->
                    <div class="text-center text-[10px] p-2 space-y-1">
                        <div>
                            Barang yang sudah dibeli tidak dapat dikembalikan.
                        </div>
                        <div>
                            Terima kasih telah berbelanja di Toko Sinar Apa!
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL AKSI -->
            <div
                class="w-[200px] p-4 flex flex-col justify-between bg-gray-50 border-l"
            >
                <div>
                    <h3 class="font-semibold text-sm mb-2">Aksi Invoice</h3>
                    <p class="text-xs text-gray-500">
                        Pilih tindakan untuk invoice ini
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <button
                        @click="downloadInvoice()"
                        class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] text-sm py-2 rounded"
                    >
                        Unduh
                    </button>

                    <button
                        @click="showInvoice = false"
                        class="bg-gray-400 text-white text-sm py-2 rounded hover:bg-gray-500"
                    >
                        Tutup
                    </button>
                </div>
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
        // printInvoice() {
        //     window.print();
        // },
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

            // =====================
            // PENGATURAN GLOBAL
            // =====================
            const pageWidth = 80;
            const paddingX = 6; // 🔥 ATUR KIRI–KANAN DI SINI
            const rightX = pageWidth - paddingX;

            const noteLines = [
                "Barang yang sudah dibeli tidak dapat dikembalikan.",
                "Terima kasih telah berbelanja di Toko Sinar Apa!",
            ];

            const pageHeight =
                (10 + d.items.length * 1.5 + 12) * lineHeight + 20;

            const doc = new jsPDF({
                unit: "mm",
                format: [pageWidth, pageHeight],
            });

            // =====================
            // POSISI ATAS
            // =====================
            let y = 10;

            // =====================
            // FONT (MONO)
            // =====================
            doc.setFont("courier");

            // =====================
            // GARIS PUTUS-PUTUS
            // =====================
            doc.setLineDash([0.7, 0.4]);

            // =====================
            // HEADER
            // =====================
            doc.setFontSize(10);
            doc.setFont(undefined, "bold");
            doc.text("TOKO SINAR APA", pageWidth / 2, y, { align: "center" });

            y += lineHeight;

            doc.setFontSize(8);
            doc.setFont(undefined, "normal");
            doc.text("Jl. Krisna, Mas, Kecamatan Ubud", pageWidth / 2, y, {
                align: "center",
            });
            y += lineHeight;
            doc.text("Telp: (0361) 123456 | 0812-3456-789", pageWidth / 2, y, {
                align: "center",
            });
            y += lineHeight;

            const lineGap = 1;
            doc.line(paddingX, y, rightX, y);
            y += lineGap;
            doc.line(paddingX, y, rightX, y);
            y += lineGap;

            y += lineHeight;

            // =====================
            // INFO INVOICE
            // =====================
            doc.setFontSize(8);

            const info = [
                ["No.Invoice", d.id_penjualan],
                ["Tanggal", d.tanggal],
                ["Kasir", d.kasir],
                ["Pelanggan", d.pelanggan],
            ];

            info.forEach(([label, value]) => {
                doc.text(label, paddingX, y);
                doc.text(`: ${value}`, paddingX + 24, y);
                y += lineHeight;
            });

            doc.line(paddingX, y, rightX, y);
            y += lineHeight;

            // =====================
            // HEADER ITEM
            // =====================
            doc.setFont(undefined, "bold");
            doc.text("Nama", paddingX, y);
            doc.text("Jumlah", paddingX + 34, y, { align: "center" });
            doc.text("Harga", paddingX + 52, y, { align: "right" });
            doc.text("Total", rightX, y, { align: "right" });

            const textOffset = 3; // jarak kecil dari garis ke teks

            y += textOffset;

            doc.line(paddingX, y, rightX, y);
            y += lineGap;
            y += lineHeight;

            doc.setFont(undefined, "normal");

            const format = (n) => Number(n).toLocaleString("id-ID");

            // =====================
            // DATA ITEM
            // =====================
            d.items.forEach((item) => {
                const namaLines = doc.splitTextToSize(item.nama, 32);

                namaLines.forEach((line, index) => {
                    doc.text(line, paddingX, y);

                    if (index === 0) {
                        doc.text(String(item.jumlah), paddingX + 34, y, {
                            align: "center",
                        });
                        doc.text(format(item.harga), paddingX + 52, y, {
                            align: "right",
                        });
                        doc.text(format(item.harga * item.jumlah), rightX, y, {
                            align: "right",
                        });
                    }

                    y += lineHeight;
                });
            });

            doc.line(paddingX, y, rightX, y);
            y += lineHeight;

            // =====================
            // TOTAL
            // =====================
            const summary = [
                ["Subtotal", d.total],
                ["Bayar", d.bayar],
                ["Kembalian", d.kembalian],
            ];

            summary.forEach(([label, value], i) => {
                if (i === 2) doc.setFont(undefined, "bold");

                doc.text(label, paddingX, y);
                doc.text(format(value), rightX, y, { align: "right" });
                y += lineHeight;
            });

            doc.setFont(undefined, "normal");

            doc.line(paddingX, y, rightX, y);
            y += lineGap;
            doc.line(paddingX, y, rightX, y);
            y += lineGap;

            y += lineHeight;

            // =====================
            // FOOTER
            // =====================
            doc.setFontSize(7);

            // maksimal lebar = pageWidth - 2 * paddingX
            const maxWidth = pageWidth - 2 * paddingX;

            noteLines.forEach((line) => {
                // split teks agar tidak melewati kiri-kanan
                const lines = doc.splitTextToSize(line, maxWidth);

                lines.forEach((l) => {
                    // center setiap baris di tengah kertas
                    doc.text(l, pageWidth / 2, y, { align: "center" });
                    y += lineHeight;
                });
            });

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
