<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Tambah Barang</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-red-500"
                >
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="simpanBarang" class="space-y-4">
                <!-- Nama Barang -->
                <div>
                    <label class="block font-medium mb-1">Nama Barang</label>
                    <input
                        ref="namaBarang"
                        v-model="form.nama_barang"
                        @keydown.enter.prevent="focusNext('satuan')"
                        @blur="validateNamaBarang"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        :class="
                            errors.nama_barang
                                ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-gray-300 focus:ring-1 '
                        "
                    />
                    <p
                        v-if="errors.nama_barang"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.nama_barang }}
                    </p>
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block font-medium mb-1">Satuan</label>
                    <select
                        ref="satuan"
                        v-model="form.satuan"
                        @keydown.enter.prevent="focusNext('kategori')"
                        @change="validateSatuan"
                        class="w-full border rounded px-3 py-2"
                        :class="
                            errors.satuan
                                ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-gray-300 focus:ring-1 '
                        "
                    >
                        <option value="">-- Pilih Satuan --</option>
                        <option v-for="s in satuanList" :key="s" :value="s">
                            {{ s }}
                        </option>
                    </select>
                    <p v-if="errors.satuan" class="text-red-500 text-sm mt-1">
                        {{ errors.satuan }}
                    </p>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block font-medium mb-1">Kategori</label>
                    <select
                        ref="kategori"
                        v-model="form.id_kategori"
                        @keydown.enter.prevent="focusNext('hargaBeli')"
                        @change="validateKategori"
                        class="w-full border rounded px-3 py-2"
                        :class="
                            errors.id_kategori
                                ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-gray-300 focus:ring-1'
                        "
                    >
                        <option value="">-- Pilih Kategori --</option>
                        <option v-for="k in kategori" :key="k.id" :value="k.id">
                            {{ k.nama_kategori }}
                        </option>
                    </select>
                    <p
                        v-if="errors.id_kategori"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.id_kategori }}
                    </p>
                </div>

                <!-- Harga -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium mb-1">Harga Beli</label>
                        <input
                            ref="hargaBeli"
                            type="text"
                            @keydown.enter.prevent="focusNext('hargaJual')"
                            :value="formatRupiah(form.harga_beli)"
                            @input="
                                form.harga_beli = unformatRupiah(
                                    $event.target.value,
                                );
                                validateHargaBeli();
                            "
                            @keydown="onlyNumber"
                            class="w-full border rounded px-3 py-2"
                            :class="
                                errors.harga_beli
                                    ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                    : 'border-gray-300 focus:ring-1 '
                            "
                        />
                        <p
                            v-if="errors.harga_beli"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.harga_beli }}
                        </p>
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Harga Jual</label>
                        <input
                            ref="hargaJual"
                            type="text"
                            @keydown.enter.prevent="focusNext('jumlah')"
                            :value="formatRupiah(form.harga_jual)"
                            @input="
                                form.harga_jual = unformatRupiah(
                                    $event.target.value,
                                );
                                validateHargaJual();
                            "
                            @keydown="onlyNumber"
                            class="w-full border rounded px-3 py-2"
                            :class="
                                errors.harga_jual
                                    ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                    : 'border-gray-300 focus:ring-1 '
                            "
                        />
                        <p
                            v-if="errors.harga_jual"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.harga_jual }}
                        </p>
                    </div>
                </div>

                <!-- Jumlah -->
                <div>
                    <label class="block font-medium mb-1">Jumlah</label>
                    <input
                        ref="jumlah"
                        v-model="form.jumlah"
                        type="number"
                        readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
                    />
                    <!-- <input
                        ref="jumlah"
                        v-model="form.jumlah"
                        @keydown.enter.prevent="focusNext('simpan')"
                        @blur="validateJumlah"
                        type="number"
                        class="w-full border rounded px-3 py-2"
                        :class="
                            errors.jumlah
                                ? 'border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-gray-300 focus:ring-1'
                        "
                    /> -->
                    <p v-if="errors.jumlah" class="text-red-500 text-sm mt-1">
                        {{ errors.jumlah }}
                    </p>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-2 pt-4">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 border rounded"
                    >
                        Batal
                    </button>
                    <button
                        ref="simpan"
                        type="submit"
                        class="bg-[#3674B5] text-white hover:bg-[#2C5F9E] px-4 py-2 rounded"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        kategori: Array,
    },
    data() {
        return {
            satuanList: ["Box", "Meter", "Pack", "Pcs", "Roll", "Set", "Unit"],
            form: {
                nama_barang: "",
                satuan: "",
                id_kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: 0,
            },
            barang: {
                nama_barang: "",
                satuan: "",
                kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: "",
            },

            errors: {
                nama_barang: "",
                satuan: "",
                kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: "",
            },
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.$refs.namaBarang?.focus();
        });
    },

    methods: {
        focusNext(refName) {
            this.$nextTick(() => {
                const el = this.$refs[refName];
                if (el) {
                    el.focus();
                }
            });
        },

        validateNamaBarang() {
            this.errors.nama_barang = this.form.nama_barang
                ? ""
                : "Nama barang wajib diisi";
        },

        validateSatuan() {
            this.errors.satuan = this.form.satuan ? "" : "Satuan wajib dipilih";
        },

        validateKategori() {
            this.errors.id_kategori = this.form.id_kategori
                ? ""
                : "Kategori wajib dipilih";
        },

        validateHargaBeli() {
            this.errors.harga_beli =
                this.form.harga_beli && this.form.harga_beli > 0
                    ? ""
                    : "Harga beli wajib diisi";
        },

        validateHargaJual() {
            this.errors.harga_jual =
                this.form.harga_jual && this.form.harga_jual > 0
                    ? ""
                    : "Harga jual wajib diisi";
        },

        // validateJumlah() {
        //     this.errors.jumlah =
        //         this.form.jumlah && this.form.jumlah > 0
        //             ? ""
        //             : "Jumlah wajib diisi";
        // },

        validateForm() {
            this.validateNamaBarang();
            this.validateSatuan();
            this.validateKategori();
            this.validateHargaBeli();
            this.validateHargaJual();
            // this.validateJumlah();

            return !Object.values(this.errors).some((e) => e);
        },

        resetForm() {
            this.barang = {
                nama_barang: "",
                satuan: "",
                kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: "",
            };

            this.errors = {
                nama_barang: "",
                satuan: "",
                kategori: "",
                harga_beli: "",
                harga_jual: "",
                jumlah: "",
            };
        },

        inputClass(error) {
            return [
                "border px-3 py-2 rounded focus:outline-none",
                error
                    ? "border-red-500 focus:ring-1 focus:ring-red-500"
                    : "border-gray-300",
            ];
        },
        onlyNumber(e) {
            // izinkan: angka, backspace, delete, panah
            if (
                !/[0-9]/.test(e.key) &&
                ![
                    "Backspace",
                    "Delete",
                    "ArrowLeft",
                    "ArrowRight",
                    "Tab",
                ].includes(e.key)
            ) {
                e.preventDefault(); //BLOKIR HURUF
            }
        },
        async simpanBarang() {
            if (!this.validateForm()) return;
            try {
                await axios.post("/api/barang", this.form);

                this.$emit("refresh");
                this.$emit("toast", "Berhasil menyimpan barang", "success");
                this.$emit("close");
            } catch (e) {
                this.$emit("toast", "Barang sudah ada", "error");
            }
        },

        formatRupiah(value) {
            if (value === null || value === undefined || value === "")
                return "";

            return Number(value).toLocaleString("id-ID");
        },

        unformatRupiah(value) {
            if (!value) return 0;

            // hapus titik, koma, dan spasi
            return Number(value.replace(/[^\d]/g, ""));
        },
    },
};
</script>
