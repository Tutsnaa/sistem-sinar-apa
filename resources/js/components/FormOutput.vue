<template>
    <div class="flex flex-col mb-4">
        <!-- Label -->
        <label v-if="label" class="mb-1 text-gray-700">
            {{ label }}
        </label>

        <!-- Teks yang ditampilkan -->
        <div
            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 min-h-[38px] flex items-center"
        >
            {{ value || "\u00a0" }}
        </div>
    </div>
</template>

<script>
export default {
    name: "FormOutput",
    props: {
        value: {
            type: String,
            default: "",
        },
        label: {
            type: String,
            default: "",
        },
    },
};
</script>

<style scoped>
/* Optional styling tambahan */
</style>
