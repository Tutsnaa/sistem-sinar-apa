<template>
    <div class="p-6 bg-white rounded-lg shadow-md w-80">
        <h2 class="text-xl font-bold mb-4">Tambah Pembelian</h2>

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

            <ul
                v-if="showSuggestions && filteredBarang.length"
                class="absolute z-10 bg-white border rounded-lg w-full mt-1 max-h-40 overflow-auto shadow-lg"
            >
                <li
                    v-for="(b, index) in filteredBarang"
                    :key="index"
                    @mousedown.prevent="selectBarang(b)"
                    class="px-4 py-2 hover:bg-gray-200 cursor-pointer"
                >
                    {{ b.nama_barang }}
                </li>
            </ul>
        </div>

        <FormOutput
            label="Nama Barang"
            :value="namaBarang"
            class="mb-4 w-full"
        />
        <FormOutput
            label="Harga Barang"
            :value="hargaBarang"
            class="mb-4 w-full"
        />

        <FormInput
            id="inputJumlah"
            v-model.number="jumlah"
            label="Jumlah"
            placeholder="Masukkan jumlah"
            type="number"
            class="mb-4 w-full"
        />

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
    components: { FormInput, FormOutput },
    data() {
        return {
            cariBarang: "",
            namaBarang: "",
            hargaBarang: "",
            jumlah: null,
            showSuggestions: false,
        };
    },
    computed: {
        filteredBarang() {
            if (!this.cariBarang) return [];

            return this.barang.filter((b) =>
                b.nama_barang
                    .toLowerCase()
                    .includes(this.cariBarang.toLowerCase())
            );
        },
    },
    methods: {
        selectBarang(b) {
            this.cariBarang = b.nama_barang;
            this.namaBarang = b.nama_barang;
            this.hargaBarang = b.harga_jual;
            this.showSuggestions = false;
        },
        hideSuggestions() {
            setTimeout(() => (this.showSuggestions = false), 100);
        },
        tambahPembelian() {
            if (!this.namaBarang || !this.jumlah) {
                alert("Lengkapi data barang dan jumlah!");
                return;
            }

            this.$emit("tambah-barang", {
                nama: this.namaBarang,
                harga: this.hargaBarang,
                jumlah: this.jumlah,
            });

            this.cariBarang = "";
            this.namaBarang = "";
            this.hargaBarang = "";
            this.jumlah = null;
        },
    },
};
</script>
