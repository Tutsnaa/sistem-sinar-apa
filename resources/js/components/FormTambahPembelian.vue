<template>
    <div class="p-6 bg-white rounded-lg shadow-md w-80">
        <h2 class="text-xl font-bold mb-4">Tambah Pembelian</h2>

        <!-- Cari Barang -->
        <div class="relative mb-4 w-full">
            <FormInput
                id="inputCariBarang"
                v-model="cariBarang"
                label="Cari Barang"
                placeholder="Masukkan nama barang"
                @focus="showSuggestions = true"
                @blur="hideSuggestions"
                class="w-full"
            />

            <!-- Autocomplete -->
            <ul
                v-if="showSuggestions && filteredBarang.length"
                class="absolute z-10 bg-white border rounded-lg w-full mt-1 max-h-40 overflow-auto shadow-lg"
            >
                <li
                    v-for="b in filteredBarang"
                    :key="b.id"
                    @mousedown.prevent="selectBarang(b)"
                    class="px-4 py-2 hover:bg-gray-200 cursor-pointer"
                >
                    {{ b.nama_barang }}
                </li>
            </ul>
        </div>

        <!-- Output -->
        <FormOutput
            label="Nama Barang"
            :value="namaBarang"
            class="mb-4 w-full"
        />

        <FormOutput
            label="Harga Barang"
            :value="formatRupiah(hargaBarang)"
            class="mb-4 w-full"
        />

        <!-- Jumlah -->
        <FormInput
            id="inputJumlah"
            v-model.number="jumlah"
            label="Jumlah"
            placeholder="Masukkan jumlah"
            type="number"
            min="1"
            class="mb-4 w-full"
        />

        <!-- Button -->
        <button
            @click="tambahPembelian"
            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full"
        >
            Tambah
        </button>
    </div>
</template>

<script>
import FormInput from "../components/FormInput.vue";
import FormOutput from "../components/FormOutput.vue";

export default {
    name: "FormTambahPembelian",

    props: {
        barang: {
            type: Array,
            required: true,
        },
    },

    components: {
        FormInput,
        FormOutput,
    },

    data() {
        return {
            cariBarang: "",
            idBarang: null,
            namaBarang: "",
            hargaBarang: "",
            jumlahBarang: 0,
            jumlah: null,
            showSuggestions: false,
        };
    },

    computed: {
        filteredBarang() {
            console.log("KEYWORD:", this.cariBarang);

            if (!this.cariBarang) return [];

            const keyword = this.cariBarang.toLowerCase();

            return this.barang.filter(
                (b) =>
                    b.nama_barang &&
                    b.status === "tersedia" &&
                    b.nama_barang.toLowerCase().includes(keyword)
            );
        },
    },

    methods: {
        selectBarang(b) {
            this.idBarang = b.id;
            this.cariBarang = b.nama_barang;
            this.idBarang = b.id;
            this.namaBarang = b.nama_barang;
            this.hargaBarang = Number(b.harga_jual);
            this.jumlahBarang = b.jumlah;
            this.showSuggestions = false;
        },

        hideSuggestions() {
            setTimeout(() => {
                this.showSuggestions = false;
            }, 100);
        },

        tambahPembelian() {
            if (!this.namaBarang || !this.jumlah || this.jumlah <= 0) {
                alert("Lengkapi data barang dan jumlah!");
                return;
            }

            if (this.jumlah > this.jumlahBarang) {
                alert("Stok tidak mencukupi!");
                return;
            }

            this.$emit("tambah-barang", {
                id_barang: this.idBarang,
                nama: this.namaBarang,
                harga: Number(this.hargaBarang),
                jumlah: this.jumlah,
            });

            // reset
            this.cariBarang = "";
            this.namaBarang = "";
            this.hargaBarang = "";
            this.jumlahBarang = 0;
            this.jumlah = null;
        },

        formatRupiah(number) {
            if (number === null || number === undefined) return "Rp 0";

            return "Rp " + Number(number).toLocaleString("id-ID");
        },
    },
};
</script>
