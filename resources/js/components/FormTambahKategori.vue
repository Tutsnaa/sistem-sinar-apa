<template>
    <div class="mb-4">
        <!-- Judul Form -->
        <h2 class="text-xl font-bold mb-4 mt-2">
            {{ isEdit ? "Ubah Kategori" : "Tambah Kategori" }}
        </h2>

        <!-- Input & Tombol -->
        <div class="flex items-center gap-2">
            <input
                type="text"
                v-model="form.nama_kategori"
                placeholder="Nama kategori"
                class="border border-gray-300 rounded px-3 py-2 w-64"
            />

            <button
                @click="submit"
                class="px-4 py-2 rounded text-white"
                :class="
                    isEdit
                        ? 'bg-[#3674B5] hover:bg-[#2C5F9E]'
                        : 'bg-[#3674B5] hover:bg-[#2C5F9E]'
                "
            >
                {{ isEdit ? "Simpan" : "Simpan" }}
            </button>

            <!-- Tombol batal saat edit -->
            <button
                v-if="isEdit"
                @click="batal"
                class="bg-gray-400 text-white hover:bg-gray-500 px-4 py-2 rounded"
            >
                Batal
            </button>
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
    },

    data() {
        return {
            form: {
                id: null,
                nama_kategori: "",
            },
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
        submit() {
            if (!this.form.nama_kategori) {
                alert("Nama kategori wajib diisi!");
                return;
            }

            if (this.isEdit) {
                // emit update
                this.$emit("update-kategori", this.form);
            } else {
                // emit tambah
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
