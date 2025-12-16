<template>
    <div class="bg-white rounded shadow p-6 mt-6">
        <h2 class="text-xl font-bold mb-4">Tambah Barang Masuk</h2>

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <!-- Cari Barang -->
            <div class="relative md:col-span-2" ref="dropdown">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Barang</label
                >
                <input
                    type="text"
                    v-model="searchBarang"
                    placeholder="Cari barang..."
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-300"
                    @focus="showDropdown = true"
                />

                <ul
                    v-if="showDropdown"
                    class="absolute z-10 bg-white border w-full max-h-40 overflow-y-auto rounded shadow mt-1"
                >
                    <li
                        v-for="barang in filteredBarang"
                        :key="barang.id"
                        @click="pilihBarang(barang)"
                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                    >
                        {{ barang.nama_barang }}
                    </li>

                    <li
                        v-if="filteredBarang.length === 0"
                        class="px-3 py-2 text-gray-500 italic"
                    >
                        Barang tidak ditemukan
                    </li>
                </ul>
            </div>

            <!-- Jumlah -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Jumlah</label
                >
                <input
                    type="number"
                    v-model.number="form.jumlah"
                    min="1"
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-300"
                    :disabled="!form.id_barang"
                />
            </div>

            <div>
                <!-- Harga Beli -->
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Harga Beli</label
                >
                <input
                    type="text"
                    :value="formatRupiah(form.harga_beli)"
                    @input="onHargaBeliInput"
                    placeholder="Harga beli"
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-300"
                    :disabled="!form.id_barang"
                />
            </div>

            <div>
                <!-- Harga Jual -->
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Harga Jual</label
                >
                <input
                    type="text"
                    :value="formatRupiah(form.harga_jual)"
                    @input="onHargaJualInput"
                    placeholder="Harga jual"
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-300"
                    :disabled="!form.id_barang"
                />
            </div>

            <!-- Total -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Total</label
                >
                <input
                    type="text"
                    :value="formatRupiah(totalPembelian)"
                    disabled
                    class="border border-gray-300 rounded px-3 py-2 w-full bg-gray-100"
                />
            </div>

            <!-- Tombol Simpan -->
            <div>
                <button
                    @click="submit"
                    :disabled="isSubmitting"
                    class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 transition-colors"
                >
                    {{ isSubmitting ? "Menyimpan..." : "Simpan" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    emits: ["refresh"],
    props: {
        editData: {
            type: Object,
            default: null,
        },
    },
    watch: {
        editData: {
            immediate: true,
            handler(newVal) {
                if (newVal) {
                    // isi form dengan data yang diedit
                    this.form.id_barang = newVal.id_barang;
                    this.form.jumlah = newVal.jumlah;
                    this.form.harga_beli = Number(newVal.harga_beli);
                    this.form.harga_jual = Number(newVal.harga_jual);
                    this.searchBarang = newVal.barang?.nama_barang || "";
                } else {
                    this.reset();
                }
            },
        },
    },
    submit() {
        if (this.isSubmitting) return;

        const idPengguna = Number(localStorage.getItem("id_pengguna"));
        if (!idPengguna) {
            alert("Session login tidak valid. Silakan login ulang.");
            return;
        }
        if (!this.form.id_barang) {
            alert("Barang harus dipilih");
            return;
        }

        this.isSubmitting = true;

        let request;
        if (this.editData && this.editData.id) {
            // Mode edit → PUT
            request = axios.put(`/api/barang-masuk/${this.editData.id}`, {
                id_barang: parseInt(this.form.id_barang),
                jumlah: parseInt(this.form.jumlah),
                harga_beli: Number(this.form.harga_beli),
                harga_jual: Number(this.form.harga_jual),
                status: this.form.status || "menunggu",
            });
        } else {
            // Mode tambah → POST
            request = axios.post("/api/barang-masuk", {
                id_barang: parseInt(this.form.id_barang),
                id_pengguna: idPengguna,
                jumlah: parseInt(this.form.jumlah),
                harga_beli: Number(this.form.harga_beli),
                harga_jual: Number(this.form.harga_jual),
                status: "menunggu",
            });
        }

        request
            .then((res) => {
                this.$emit("refresh", res.data.data ?? res.data);
                alert(
                    this.editData
                        ? "Barang berhasil diubah"
                        : "Barang masuk berhasil ditambahkan"
                );
                this.reset();
                this.$emit("resetEdit");
            })
            .catch((err) => {
                console.error(err.response?.data);
                alert("Validasi gagal, cek console");
            })
            .finally(() => {
                this.isSubmitting = false;
            });
    },

    data() {
        return {
            listBarang: [],
            searchBarang: "",
            showDropdown: false,
            isSubmitting: false,
            form: {
                id_barang: null,
                jumlah: "",
                harga_beli: 0,
                harga_jual: 0,
            },
        };
    },

    computed: {
        filteredBarang() {
            const key = this.searchBarang.toLowerCase();
            return this.listBarang.filter((b) =>
                b.nama_barang.toLowerCase().includes(key)
            );
        },

        totalPembelian() {
            return this.form.jumlah * this.form.harga_beli;
        },
    },

    mounted() {
        this.getBarang();
        document.addEventListener("click", this.handleClickOutside);
    },

    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },

    methods: {
        getBarang() {
            axios.get("/api/barang").then((res) => {
                this.listBarang = res.data.data ?? [];
            });
        },

        pilihBarang(barang) {
            this.form.id_barang = barang.id;
            this.searchBarang = barang.nama_barang;
            this.form.harga_beli = barang.harga_beli ?? 0;
            this.form.harga_jual = barang.harga_jual ?? 0;
            this.form.jumlah = 0;
            this.showDropdown = false;
        },

        handleClickOutside(e) {
            if (!this.$refs.dropdown?.contains(e.target)) {
                this.showDropdown = false;
            }
        },

        onHargaBeliInput(e) {
            this.form.harga_beli = this.parseRupiah(e.target.value);
        },

        onHargaJualInput(e) {
            this.form.harga_jual = this.parseRupiah(e.target.value);
        },

        submit() {
            if (this.isSubmitting) return;

            const idPengguna = Number(localStorage.getItem("id_pengguna"));
            if (!idPengguna) {
                alert("Session login tidak valid. Silakan login ulang.");
                return;
            }

            if (!this.form.id_barang) {
                alert("Barang harus dipilih");
                return;
            }

            this.isSubmitting = true;

            let request;
            if (this.editData) {
                // Mode Ubah
                request = axios.put(`/api/barang-masuk/${this.editData.id}`, {
                    id_barang: parseInt(this.form.id_barang),
                    jumlah: parseInt(this.form.jumlah),
                    harga_beli: Number(this.form.harga_beli),
                    harga_jual: Number(this.form.harga_jual),
                    status: this.form.status || "menunggu",
                });
            } else {
                // Mode Tambah
                request = axios.post("/api/barang-masuk", {
                    id_barang: parseInt(this.form.id_barang),
                    id_pengguna: idPengguna,
                    jumlah: parseInt(this.form.jumlah),
                    harga_beli: Number(this.form.harga_beli),
                    harga_jual: Number(this.form.harga_jual),
                    status: "menunggu",
                });
            }

            request
                .then((res) => {
                    const dataBaru = res.data.data ?? res.data; // ambil data yang diubah atau baru
                    this.$emit("refresh", dataBaru); // kirim ke parent
                    alert(
                        this.editData
                            ? "Barang berhasil diubah"
                            : "Barang masuk berhasil ditambahkan"
                    );
                    this.reset();
                    this.$emit("resetEdit");
                })
                .catch((err) => {
                    console.error(err.response?.data);
                    alert("Validasi gagal, cek console");
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },

        reset() {
            this.form = {
                id_barang: null,
                jumlah: 0,
                harga_beli: 0,
                harga_jual: 0,
            };
            this.searchBarang = "";
        },
        // Format angka menjadi Rupiah
        formatRupiah(val) {
            return val !== null && val !== undefined
                ? "Rp " + Number(val).toLocaleString("id-ID")
                : "";
        },

        // Mengubah input menjadi angka + update tampilan
        onHargaBeliInput(e) {
            const angka = e.target.value.replace(/[^0-9]/g, "");
            this.form.harga_beli = angka ? parseInt(angka) : 0;
            // Update tampilan agar selalu format Rupiah
            e.target.value = this.formatRupiah(this.form.harga_beli);
        },

        onHargaJualInput(e) {
            const angka = e.target.value.replace(/[^0-9]/g, "");
            this.form.harga_jual = angka ? parseInt(angka) : 0;
            e.target.value = this.formatRupiah(this.form.harga_jual);
        },
    },
};
</script>
