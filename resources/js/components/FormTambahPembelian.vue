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
            class="mt-4 bg-[#3674B5] text-white px-4 py-2 rounded-lg hover:bg-[#2C5F9E] w-full transition font-semibold"
        >
            Tambah
        </button>
    </div>
    <!-- NOTIFICATION -->
    <div
        v-if="toast.show"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-slide-down text-white"
        :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
    >
        <!-- ICON -->
        <!-- SUCCESS -->
        <svg
            v-if="toast.type === 'success'"
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
            />
        </svg>

        <!-- ERROR -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"
            />
        </svg>

        <span class="font-medium text-sm">
            {{ toast.message }}
        </span>
    </div>
</template>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.animate-slide-down {
    animation: slideDown 0.35s ease-out;
}
</style>

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
            toast: {
                show: false,
                message: "",
                type: "success", // success | error
            },
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
                    b.nama_barang.toLowerCase().includes(keyword),
            );
        },
    },

    methods: {
        showToast(message, type = "success") {
            this.toast = {
                show: true,
                message,
                type,
            };

            setTimeout(() => {
                this.toast.show = false;
            }, 2000);
        },

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
                this.showToast("Lengkapi data barang dan jumlah!", "error");
                return;
            }

            if (this.jumlah > this.jumlahBarang) {
                this.showToast("Stok tidak mencukupi!", "error");
                return;
            }

            this.$emit("tambah-barang", {
                id_barang: this.idBarang,
                nama: this.namaBarang,
                harga: Number(this.hargaBarang),
                jumlah: this.jumlah,
            });

            this.$emit("toast", "Barang berhasil ditambahkan", "success");

            // reset form
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
