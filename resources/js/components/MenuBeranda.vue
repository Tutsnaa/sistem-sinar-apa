<template>
    <div
        :class="[
            'bg-white rounded-lg shadow-md hover:shadow-lg transition cursor-pointer hover:bg-[#b5d9ff]',
            cardClass,
        ]"
    >
        <!-- Icon -->
        <div :class="'mb-2 flex justify-center'">
            <slot name="icon"></slot>
        </div>

        <!-- Teks -->
        <div :class="['text-center text-gray-800 font-semibold', fontsize]">
            {{ title }}
        </div>
    </div>
</template>

<script>
export default {
    name: "MenuBeranda",
    props: {
        title: { type: String, required: true },
        fontsize: { type: String, default: "text-lg" },
        cardClass: {
            type: String,
            default: "h-40 w-80 p-4 flex flex-col items-center justify-center",
        },
    },
};
</script>
