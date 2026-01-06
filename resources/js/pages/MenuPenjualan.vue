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
                placeholder="Masukkan Nama Pelanggan"
                @update:modelValue="errorNama = ''"
                :error="errorNama"
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
                                class="whitespace-normal wrap-break-words leading-tight"
                            >
                                {{ item.nama }}
                            </div>

                            <!-- Jumlah -->
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
            errorNama: "",
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
        printInvoice() {
            this.$nextTick(() => {
                window.print();
            });
        },
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
            //ERROR
            this.errorNama = "";

            if (!this.namapelanggan) {
                this.errorNama = "Nama pengguna wajib diisi";
                return;
            }

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

            try {
                const res = await fetch("/api/penjualan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    alert(data.message || "Gagal simpan");
                    return;
                }

                // ===== SIAPKAN DATA INVOICE =====
                this.invoiceData = {
                    id_penjualan: data.id_penjualan,
                    tanggal: this.tanggal,
                    kasir: this.nama,
                    pelanggan: this.namapelanggan,
                    items: [...this.daftarPembelian], // clone supaya reactive
                    total: this.totalHarga,
                    bayar: payloadChild.bayar,
                    kembalian: payloadChild.bayar - this.totalHarga,
                };

                // ===== TAMPILKAN POPUP INVOICE =====
                this.showInvoice = true;

                // ===== OPSIONAL: jika ingin download otomatis setelah popup muncul =====
                // this.$nextTick(() => {
                //     this.downloadInvoice();
                // });

                // ===== RESET FORM =====
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
            let y = 10; // 🔥 ATUR ATAS DI SINI

            // =====================
            // FONT (MONO)
            // =====================
            doc.setFont("courier");

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

            doc.line(paddingX, y, rightX, y);
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
    },
};
</script>
