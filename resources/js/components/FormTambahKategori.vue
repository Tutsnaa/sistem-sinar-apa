<template>
    <div class="mb-4">
        <!-- Judul Form -->
        <h2 class="text-xl font-bold mb-4 mt-2">
            {{ isEdit ? "Ubah Kategori" : "Tambah Kategori" }}
        </h2>

        <div class="mb-4 relative">
            <!-- Input -->
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    v-model="form.nama_kategori"
                    placeholder="Nama kategori"
                    class="border rounded px-3 py-2 w-64"
                    :class="errorNama ? 'border-red-500' : 'border-gray-300'"
                    @focus="errorNama = ''"
                />

                <button
                    @click="submit"
                    class="px-4 py-2 rounded text-white bg-[#3674B5] hover:bg-[#2C5F9E]"
                >
                    Simpan
                </button>

                <button
                    v-if="isEdit"
                    @click="batal"
                    class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
                >
                    Batal
                </button>
            </div>

            <!-- PESAN ERROR -->
            <p
                v-if="errorNama"
                class="absolute left-0 top-full mt-0.5 text-[15px] text-red-500 z-50"
            >
                {{ errorNama }}
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormTambahKategori",

    props: {
        isEdit: {
            type: Boolean,
            default: false,
        },
        dataEdit: {
            type: Object,
            default: null,
        },
        error: {
            type: String,
            default: "",
        },
    },

    data() {
        return {
            form: {
                id: null,
                nama_kategori: "",
            },
            toast: "",
            errorNama: "",
        };
    },

    watch: {
        // Saat klik tombol Ubah, data otomatis masuk ke form
        dataEdit: {
            immediate: true,
            handler(val) {
                if (val) {
                    this.form.id = val.id;
                    this.form.nama_kategori = val.nama_kategori;
                }
            },
        },
    },

    methods: {
        showToast(pesan) {
            this.toast = pesan;
            setTimeout(() => {
                this.toast = "";
            }, 3000);
        },
        submit() {
            this.errorNama = "";

            if (!this.form.nama_kategori) {
                this.errorNama = "Nama kategori wajib diisi";
                return;
            }

            if (this.isEdit) {
                this.$emit("update-kategori", this.form);
            } else {
                this.$emit("tambah-kategori", {
                    nama_kategori: this.form.nama_kategori,
                });
            }

            this.resetForm();
        },

        batal() {
            this.resetForm();
            this.$emit("batal-edit");
        },

        resetForm() {
            this.form.id = null;
            this.form.nama_kategori = "";
        },
    },
};
</script>
