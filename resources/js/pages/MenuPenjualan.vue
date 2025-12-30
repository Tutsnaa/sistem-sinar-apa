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
                <span class="material-icons" style="font-size: 25px"
                    >arrow_back</span
                >
                Kembali
            </h1>

            <h1 class="text-xl font-semibold">PENJUALAN</h1>

            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- Konten Utama -->
        <div class="px-8 mt-4 flex flex-wrap gap-4 pt-24">
            <FormOutput
                class="w-64 rounded-10px"
                label="Pengguna"
                :value="nama"
            />

            <FormOutput
                class="w-40 rounded-10px"
                label="Tanggal"
                :value="tanggal"
            />

            <FormInput
                id="namaPelanggan"
                class="w-80 rounded-10px"
                label="Pelanggan"
                v-model="namapelanggan"
            />
        </div>

        <div class="px-8 mt-6 flex flex-row gap-4 items-start">
            <!-- Tambah Pembelian -->
            <FormTambahPembelian
                :barang="barang"
                @tambah-barang="tambahBarang"
                class="w-80"
            />

            <!-- Daftar Pembelian -->
            <DaftarPembelian
                :key="resetKey"
                :daftarPembelian="daftarPembelian"
                @edit="onEditPembelian"
                @simpan-penjualan="simpanPenjualan"
            />
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
import { jsPDF } from "jspdf";
import FormInput from "../components/FormInput.vue";
import FormOutput from "../components/FormOutput.vue";
import DaftarPembelian from "../components/DaftarPembelian.vue";
import FormTambahPembelian from "../components/FormTambahPembelian.vue";

export default {
    components: {
        FormInput,
        FormOutput,
        DaftarPembelian,
        FormTambahPembelian,
    },

    data() {
        return {
            nama: "",
            foto: "",
            tanggal: "",
            namapelanggan: "",
            barang: [],
            daftarPembelian: [],
            daftarBarang: [],
            resetKey: 0,
            showInvoice: false,
            invoiceData: null,
        };
    },

    computed: {
        totalHarga() {
            return this.daftarPembelian.reduce(
                (sum, item) => sum + item.harga * item.jumlah,
                0
            );
        },
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        const t = new Date();
        const hari = String(t.getDate()).padStart(2, "0");
        const bulan = String(t.getMonth() + 1).padStart(2, "0");
        const tahun = t.getFullYear();
        this.tanggal = `${hari}-${bulan}-${tahun}`;

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.foto = foto;
        this.getBarang();
    },

    methods: {
        goBack() {
            const role = localStorage.getItem("role");

            if (role === "pemilik_toko") this.$router.push("/beranda-pemilik");
            else if (role === "karyawan")
                this.$router.push("/beranda-karyawan");
            else this.$router.push("/login");
        },

        async getBarang() {
            try {
                const res = await fetch("/api/barang");
                const json = await res.json();
                this.barang = json.data; // 🔥 PENTING
                console.log(this.barang);
            } catch (error) {
                console.error("Gagal ambil data barang", error);
            }
        },

        tambahBarang(data) {
            const index = this.daftarPembelian.findIndex(
                (item) => item.nama === data.nama
            );

            if (index !== -1) {
                // jika barang sudah ada → tambah jumlah
                this.daftarPembelian[index].jumlah += data.jumlah;
            } else {
                // jika belum → push baru
                this.daftarPembelian.push({
                    id_barang: data.id_barang,
                    nama: data.nama,
                    harga: data.harga,
                    jumlah: data.jumlah,
                });
            }
            console.log(this.daftarPembelian);
        },

        async simpanPenjualan(payloadChild) {
            const idPengguna = localStorage.getItem("id_pengguna");

            if (!idPengguna) {
                alert("Session login tidak valid");
                this.$router.push("/login");
                return;
            }

            const payload = {
                id_pengguna: Number(idPengguna),
                nama_pelanggan: this.namapelanggan,
                bayar: payloadChild.bayar,
                items: this.daftarPembelian.map((item) => ({
                    id_barang: item.id_barang,
                    harga: item.harga,
                    jumlah: item.jumlah,
                })),
            };

            console.log("PAYLOAD:", payload);

            try {
                const res = await fetch("/api/penjualan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                const text = await res.text();
                console.log("RAW RESPONSE:", text);

                const data = JSON.parse(text);

                if (!res.ok) {
                    alert(data.message || "Gagal simpan");
                    return;
                }

                // ===== INVOICE =====
                this.invoiceData = {
                    id_penjualan: data.id_penjualan,
                    tanggal: this.tanggal,
                    kasir: this.nama,
                    pelanggan: this.namapelanggan,
                    items: this.daftarPembelian,
                    total: this.totalHarga,
                    bayar: payloadChild.bayar,
                    kembalian: payloadChild.bayar - this.totalHarga,
                };

                this.showInvoice = true;

                this.daftarPembelian = [];
                this.namapelanggan = "";
                this.resetKey++;
            } catch (err) {
                console.error(err);
                alert("Server error");
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

        //     downloadInvoice() {
        //         const d = this.invoiceData;

        //         // format rupiah
        //         const rupiah = (n) => "Rp" + Number(n).toLocaleString("id-ID");

        //         // daftar barang
        //         const items = d.items
        //             .map(
        //                 (i) => `
        //         <div class="row">
        //             <div class="name">${i.nama}</div>
        //             <div class="qty">${i.jumlah}</div>
        //             <div class="price">${rupiah(i.harga)}</div>
        //             <div class="total">${rupiah(i.harga * i.jumlah)}</div>
        //         </div>
        //     `
        //             )
        //             .join("");

        //         const html = `
        // <div class="invoice">
        //     <div class="center bold">TOKO SINAR APA</div>
        //     <div class="center">Jl. Krisna, Mas, Ubud</div>
        //     <div class="center">Telp: 0812-3456-789</div>

        //     <div class="line"></div>

        //     <div>No. Invoice : ${String(d.id_penjualan).padStart(4, "0")}</div>
        //     <div>Tanggal    : ${d.tanggal}</div>
        //     <div>Kasir      : ${d.kasir}</div>

        //     <div class="line"></div>

        //     <div>Nama Pelanggan:</div>
        //     <div class="bold">${d.pelanggan}</div>

        //     <div class="line"></div>

        //     <div class="row header">
        //         <div class="name">Nama</div>
        //         <div class="qty">Q</div>
        //         <div class="price">Harga</div>
        //         <div class="total">Total</div>
        //     </div>

        //     ${items}

        //     <div class="line"></div>

        //     <div class="summary">
        //         <div><span>Total</span><span>${rupiah(d.total)}</span></div>
        //         <div><span>Bayar</span><span>${rupiah(d.bayar)}</span></div>
        //         <div><span>Kembali</span><span>${rupiah(d.kembalian)}</span></div>
        //     </div>

        //     <div class="line"></div>

        //     <div class="note">
        //         Barang yang sudah dibeli tidak dapat dikembalikan.<br>
        //         Terima kasih telah berbelanja 🙏
        //     </div>
        // </div>
        // `;

        //         const win = window.open("", "", "width=260,height=600");
        //         win.document.write(`
        //     <html>
        //     <head>
        //         <title>Struk Penjualan</title>
        //         <style>
        //             @media print {
        //                 body { margin: 0; }
        //             }
        //             body {
        //                 font-family: monospace;
        //                 padding: 4px;
        //             }
        //             .invoice {
        //                 width: 220px; /* 🔥 UKURAN INDOMARET */
        //                 border: 1px solid #000;
        //                 padding: 6px;
        //                 font-size: 10px;
        //             }
        //             .center {
        //                 text-align: center;
        //             }
        //             .bold {
        //                 font-weight: bold;
        //             }
        //             .line {
        //                 border-top: 1px dashed #000;
        //                 margin: 6px 0;
        //             }
        //             .row {
        //                 display: flex;
        //                 justify-content: space-between;
        //                 font-size: 9px;
        //             }
        //             .header {
        //                 font-weight: bold;
        //                 border-bottom: 1px solid #000;
        //                 margin-bottom: 3px;
        //             }
        //             .name {
        //                 width: 40%;
        //             }
        //             .qty {
        //                 width: 10%;
        //                 text-align: center;
        //             }
        //             .price {
        //                 width: 20%;
        //                 text-align: right;
        //             }
        //             .total {
        //                 width: 30%;
        //                 text-align: right;
        //             }
        //             .summary div {
        //                 display: flex;
        //                 justify-content: space-between;
        //                 font-size: 10px;
        //             }
        //             .note {
        //                 text-align: center;
        //                 font-size: 9px;
        //             }
        //         </style>
        //     </head>
        //     <body>${html}</body>
        //     </html>
        // `);

        //         win.document.close();
        //         win.focus();
        //         win.print();
        //     },
    },
};
</script>
