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
            const d = this.invoiceData;
            const rupiah = (n) => "Rp " + Number(n).toLocaleString("id-ID");

            const itemsHtml = (d.items || [])
                .map(
                    (i) => `<div class="row">
                        <div class="name">${i.nama}</div>
                        <div class="qty">${i.jumlah}</div>
                        <div class="price">${rupiah(i.harga)}</div>
                        <div class="total">${rupiah(i.harga * i.jumlah)}</div>
                    </div>`
                )
                .join("");

            const html = `
        <div class="invoice">
            <div class="center bold">TOKO SINAR APA</div>
            <div class="center">Jl. Krisna, Mas, Ubud</div>
            <div class="center">Telp: 0812-3456-789</div>
            <div class="line"></div>
            <div>No. Invoice : ${String(d.id_penjualan).padStart(4, "0")}</div>
            <div>Tanggal    : ${d.tanggal}</div>
            <div>Kasir      : ${d.kasir}</div>
            <div class="line"></div>
            <div>Nama Pelanggan:</div>
            <div class="bold">${d.pelanggan}</div>
            <div class="line"></div>
            <div class="row header">
                <div class="name">Nama</div>
                <div class="qty">Q</div>
                <div class="price">Harga</div>
                <div class="total">Total</div>
            </div>
            ${itemsHtml}
            <div class="line"></div>
            <div class="summary">
                <div><span>Total</span><span>${rupiah(d.total)}</span></div>
                <div><span>Bayar</span><span>${rupiah(d.bayar)}</span></div>
                <div><span>Kembali</span><span>${rupiah(
                    d.kembalian
                )}</span></div>
            </div>
            <div class="line"></div>
            <div class="note">Barang yang sudah dibeli tidak dapat dikembalikan.<br>Terima kasih telah berbelanja 🙏</div>
        </div>
        `;

            const win = window.open("", "", "width=260,height=600");
            win.document.write(`<html><head><title>Struk Penjualan</title>
            <style>
                body { font-family: monospace; padding:4px; }
                .invoice { width:220px; border:1px solid #000; padding:6px; font-size:10px; }
                .center { text-align:center; }
                .bold { font-weight:bold; }
                .line { border-top:1px dashed #000; margin:6px 0; }
                .row { display:flex; justify-content:space-between; font-size:9px; }
                .header { font-weight:bold; border-bottom:1px solid #000; margin-bottom:3px; }
                .name { width:40%; }
                .qty { width:10%; text-align:center; }
                .price { width:20%; text-align:right; }
                .total { width:30%; text-align:right; }
                .summary div { display:flex; justify-content:space-between; font-size:10px; }
                .note { text-align:center; font-size:9px; }
            </style>
        </head><body>${html}</body></html>`);
            win.document.close();
            win.focus();
            win.print();
        },

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
