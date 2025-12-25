<template>
    <div class="min-h-screen bg-gray-100 pt-28 px-6">
        <!-- Navbar -->
        <nav
            class="bg-[#3674B5] text-white px-6 py-6 flex justify-between items-center shadow fixed top-0 left-0 w-full z-50"
        >
            <h1
                @click="goBack"
                class="flex items-center gap-3 text-xl font-semibold hover:text-gray-200 cursor-pointer"
            >
                <span class="material-icons" style="font-size: 25px">
                    arrow_back
                </span>
                Kembali
            </h1>

            <h1 class="text-xl font-semibold">LAPORAN</h1>

            <div class="flex items-center gap-4">
                <img
                    v-if="foto"
                    :src="`/foto_pengguna/${foto}`"
                    class="w-10 h-10 rounded-full object-cover"
                />
                <span>{{ nama }}</span>
            </div>
        </nav>

        <!-- Filter Bulan -->
        <div class="bg-white p-4 rounded shadow mb-6 max-w-md">
            <label class="block mb-2 font-semibold text-gray-700">
                Pilih Bulan
            </label>
            <input
                type="month"
                v-model="periode"
                @change="ambilLaporan"
                class="border px-3 py-2 rounded w-full"
            />
        </div>

        <!-- Grafik Barang Terlaris -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-lg font-semibold mb-3">Grafik Barang Terlaris</h2>

            <!-- Bungkus canvas -->
            <div class="relative h-80">
                <canvas ref="chartBarang"></canvas>
            </div>
        </div>

        <!-- Tabel Barang Terlaris -->
        <h2 class="text-lg font-semibold mb-3">Grafik Barang Terlaris</h2>
        <div class="max-h-[500px] overflow-y-auto border rounded">
            <table class="min-w-full border bg-white">
                <thead class="bg-gray-100 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-2 border text-center">No</th>
                        <th class="px-4 py-2 border">Nama Barang</th>
                        <th class="px-4 py-2 border text-center">
                            Total Terjual
                        </th>
                        <th class="px-4 py-2 border text-right">
                            Total Pendapatan
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in laporan"
                        :key="index"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2 border text-center">
                            {{ index + 1 }}
                        </td>

                        <td class="px-4 py-2 border">
                            {{ item.nama_barang }}
                        </td>

                        <td class="px-4 py-2 border text-center">
                            {{ item.total_terjual }}
                        </td>

                        <td class="px-4 py-2 border text-right">
                            Rp {{ formatRupiah(item.total_pendapatan) }}
                        </td>
                    </tr>

                    <tr v-if="laporan.length === 0">
                        <td colspan="4" class="text-center py-6 text-gray-500">
                            Data tidak tersedia
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from "chart.js";

Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
);

export default {
    data() {
        return {
            nama: "",
            foto: "",
            periode: new Date().toISOString().slice(0, 7), // yyyy-mm
            laporan: [],
            chart: null,
        };
    },

    mounted() {
        const nama = localStorage.getItem("nama");
        const role = localStorage.getItem("role");
        const foto = localStorage.getItem("foto");

        if (!nama || !role) {
            this.$router.push("/login");
            return;
        }

        this.nama = nama;
        this.foto = foto;

        this.ambilLaporan();
    },

    computed: {
        top10Laporan() {
            return this.laporan
                .slice() // copy array
                .sort((a, b) => b.total_terjual - a.total_terjual)
                .slice(0, 10);
        },
    },

    methods: {
        goBack() {
            const role = localStorage.getItem("role");

            if (role === "pemilik_toko") {
                this.$router.push("/beranda-pemilik");
            } else if (role === "karyawan") {
                this.$router.push("/beranda-karyawan");
            } else {
                this.$router.push("/login");
            }
        },

        ambilLaporan() {
            const [tahun, bulan] = this.periode.split("-");

            axios
                .get("/api/barang-terlaris", {
                    params: { bulan, tahun },
                })
                .then((res) => {
                    this.laporan = res.data.data || [];
                    this.$nextTick(() => {
                        this.renderChart(); // 🔥 render grafik
                    });
                })
                .catch(() => {
                    this.laporan = [];
                    if (this.chart) this.chart.destroy();
                });
        },

        formatRupiah(angka) {
            return new Intl.NumberFormat("id-ID").format(angka);
        },

        renderChart() {
            if (this.chart) {
                this.chart.destroy();
            }

            if (!this.top10Laporan || this.top10Laporan.length === 0) return;

            const labels = this.top10Laporan.map((item) => item.nama_barang);

            const dataTerjual = this.top10Laporan.map(
                (item) => item.total_terjual
            );

            const ctx = this.$refs.chartBarang.getContext("2d");

            this.chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels,
                    datasets: [
                        {
                            label: "Jumlah Terjual",
                            data: dataTerjual,
                            backgroundColor: "#3674B5",
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });
        },
    },
};
</script>
