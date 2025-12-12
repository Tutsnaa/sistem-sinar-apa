<template>
    <button
        type="submit"
        class="w-full bg-[#3674B5] text-white font-semibold py-2 rounded-md hover:bg-gray-800 transition"
    >
        <slot />
    </button>
</template>

<script>
export default {
    name: "ButtonPrimary",
};
</script>
