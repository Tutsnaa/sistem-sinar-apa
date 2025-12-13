<template>
    <div>
        <!-- Judul Daftar Kategori -->
        <h2 class="text-xl font-bold mb-4 mt-2">Daftar Kategori</h2>

        <!-- Tabel Daftar Kategori -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b text-center">
                            Id Kategori
                        </th>
                        <th class="px-4 py-2 border-b text-left">
                            Nama Kategori
                        </th>
                        <th class="px-4 py-2 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(kategori, index) in kategoriList"
                        :key="kategori.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="px-4 py-2 text-center">{{ index + 1 }}</td>
                        <td class="px-4 py-2 text-left">
                            {{ kategori.nama_kategori }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex justify-center">
                                <button
                                    @click="$emit('hapus', kategori.id)"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Jika daftar kategori kosong -->
                    <tr v-if="kategoriList.length === 0">
                        <td class="px-4 py-2 text-center" colspan="3">
                            Belum ada kategori
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "DaftarKategori",
    props: {
        kategoriList: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
/* Tambahan opsional untuk hover row dan tombol */
tr:hover {
    background-color: #f9fafb;
}
button {
    transition: all 0.2s;
}
</style>
