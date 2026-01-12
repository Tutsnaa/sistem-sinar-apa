<template>
    <div>
        <h2 class="text-xl font-bold mb-4 mt-2">Daftar Kategori</h2>
        <div
            class="max-h-[400px] overflow-y-auto border border-gray-300 rounded"
        >
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="border px-4 py-2 border-b text-center">
                            No
                        </th>
                        <th class="border px-4 py-2 border-b text-center">
                            Nama Kategori
                        </th>
                        <th class="border px-4 py-2 border-b text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(kategori, index) in kategoriList"
                        :key="kategori.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="border px-4 py-2 text-center">
                            {{ index + 1 }}
                        </td>
                        <td class="border px-4 py-2 text-left">
                            {{ kategori.nama_kategori }}
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                <button
                                    @click="$emit('ubah', kategori)"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
                                >
                                    Ubah
                                </button>
                                <button
                                    @click="$emit('hapus', kategori.id)"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                >
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
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
