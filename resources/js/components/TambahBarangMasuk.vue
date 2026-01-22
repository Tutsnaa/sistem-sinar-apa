<template>
    <div class="bg-white rounded shadow p-6 mt-6">
        <h2 class="text-xl font-bold mb-4">
            {{ editData ? "Ubah Barang Masuk" : "Tambah Barang Masuk" }}
        </h2>

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
                    @refresh="getBarang"
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
                        class="px-3 py-3 text-gray-500 italic text-center"
                    >
                        Barang tidak ditemukan.
                        <button
                            @click="openTambahBarang"
                            class="block w-full mt-2 text-[#3674B5] hover:underline font-medium"
                        >
                            + Tambah barang baru
                        </button>
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
                    :disabled="!form.id_barang || isLocked"
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
                    :disabled="!form.id_barang || isLocked"
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
                    :disabled="!form.id_barang || isLocked"
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
            <div class="flex justify-between gap-5">
                <button
                    @click="submit"
                    :disabled="!form.id_barang || isLocked"
                    class="w-full bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded disabled:opacity-50 transition-colors"
                >
                    {{ isLocked ? "Terkunci" : "Simpan" }}
                </button>
                <button
                    @click="batal"
                    class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>
    <FormTambahBarang
        v-if="showForm"
        :kategori="kategori"
        @close="showForm = false"
        @success="handleBarangAdded"
        @refresh="getBarang"
    />
</template>

<script>
import FormTambahBarang from "@/components/FormTambahBarang.vue";
import axios from "axios";

export default {
    components: {
        FormTambahBarang,
    },
    emits: ["refresh", "resetEdit", "toast"],
    props: {
        editData: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            listBarang: [],
            kategori: [],
            searchBarang: "",
            showDropdown: false,
            isSubmitting: false,
            showForm: false,
            form: {
                id_barang: null,
                jumlah: 0,
                harga_beli: 0,
                harga_jual: 0,
            },
        };
    },

    computed: {
        isLocked() {
            return this.editData?.status === "Diterima";
        },
        filteredBarang() {
            const key = this.searchBarang.toLowerCase();
            return this.listBarang.filter((b) =>
                b.nama_barang.toLowerCase().includes(key),
            );
        },
        totalPembelian() {
            return this.form.jumlah * this.form.harga_beli;
        },
    },

    watch: {
        editData: {
            immediate: true,
            handler(val) {
                if (val) {
                    this.form.id = val.id;
                    this.form.id_barang = val.id_barang;
                    this.form.jumlah = val.jumlah;
                    this.form.harga_beli = Number(val.harga_beli);
                    this.form.harga_jual = Number(val.harga_jual);
                    this.form.status = val.status;
                    this.searchBarang = val.barang?.nama_barang || "";
                } else {
                    this.reset();
                }
            },
        },
    },

    mounted() {
        this.getBarang();
        this.getKategori(); // ✅ SEKARANG TERPANGGIL
        document.addEventListener("click", this.handleClickOutside);
    },

    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },

    methods: {
        resetForm() {
            this.form = {
                id_barang: null,
                jumlah: 0,
                harga_beli: 0,
                harga_jual: 0,
            };

            this.searchBarang = "";
            this.showDropdown = false;
            this.isLocked = false;
            this.bayar = 0;
        },

        batal() {
            this.resetForm();
            this.$emit("resetEdit");
        },

        getBarang() {
            axios.get("/api/barang").then((res) => {
                this.listBarang = res.data.data ?? [];
            });
        },

        getKategori() {
            axios.get("/api/kategori").then((res) => {
                this.kategori = res.data.data ?? res.data;
            });
        },

        openTambahBarang() {
            this.showForm = true;
            this.showDropdown = false;
        },

        handleBarangAdded() {
            this.showForm = false;
            this.getBarang();

            this.$emit("toast", "Data Barang Berhasil", "success");
        },

        pilihBarang(barang) {
            this.form.id_barang = barang.id;
            this.searchBarang = barang.nama_barang;
            this.form.harga_beli = barang.harga_beli ?? 0;
            this.form.harga_jual = barang.harga_jual ?? 0;
            this.form.jumlah = barang.jumlah ?? 0;
            this.showDropdown = false;
        },

        handleClickOutside(e) {
            if (!this.$refs.dropdown?.contains(e.target)) {
                this.showDropdown = false;
            }
        },

        submit() {
            if (this.isSubmitting) return;

            const idPengguna = Number(localStorage.getItem("id_pengguna"));
            if (!idPengguna || !this.form.id_barang) {
                this.$emit("toast", "Barang harus dipilih", "error");
                return;
            }

            this.isSubmitting = true;

            const payload = {
                id_barang: this.form.id_barang,
                id_pengguna: idPengguna,
                jumlah: this.form.jumlah,
                harga_beli: this.form.harga_beli,
                harga_jual: this.form.harga_jual,
                status: "Menunggu",
            };

            // ==========================
            // ✏️ MODE EDIT
            // ==========================
            if (this.editData && this.editData.id) {
                axios
                    .put(`/api/barang-masuk/${this.editData.id}`, payload)
                    .then((res) => {
                        this.$emit("refresh", res.data.data);
                        this.$emit("resetEdit");
                        this.$emit(
                            "toast",
                            "Data berhasil diperbarui",
                            "success",
                        );
                        this.reset();
                    })
                    .catch(() => {
                        this.$emit("toast", "Gagal mengedit data", "error");
                    })
                    .finally(() => (this.isSubmitting = false));

                // ==========================
                // ➕ MODE TAMBAH
                // ==========================
            } else {
                axios
                    .post("/api/barang-masuk", payload)
                    .then((res) => {
                        this.$emit("refresh", res.data.data);
                        this.$emit(
                            "toast",
                            "Barang masuk berhasil ditambahkan",
                            "success",
                        );
                        this.reset();
                    })
                    .catch(() => {
                        this.$emit("toast", "Gagal menambah data", "error");
                    })
                    .finally(() => (this.isSubmitting = false));
            }
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

        formatRupiah(val) {
            return "Rp " + Number(val).toLocaleString("id-ID");
        },

        onHargaBeliInput(e) {
            this.form.harga_beli = Number(e.target.value.replace(/\D/g, ""));
        },

        onHargaJualInput(e) {
            this.form.harga_jual = Number(e.target.value.replace(/\D/g, ""));
        },
    },
};
</script>
