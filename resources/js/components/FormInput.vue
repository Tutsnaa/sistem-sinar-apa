<template>
    <div class="relative mb-4">
        <label :for="id" class="block text-gray-700 mb-1 font-medium">
            {{ label }}
        </label>

        <input
            :id="id"
            :type="type"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @focus="$emit('focus')"
            @blur="$emit('blur')"
            :class="[
                'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2',
                error
                    ? 'border-red-500 focus:ring-red-300'
                    : 'border-gray-300 focus:ring-blue-400',
            ]"
        />

        <!-- PESAN ERROR -->
        <p
            v-if="error"
            class="left-0 top-full mt-0.5 text-[15px] text-red-500 z-50"
        >
            {{ error }}
        </p>
    </div>
</template>

<script>
export default {
    name: "FormInput",
    props: {
        id: { type: String, required: true },
        label: { type: String, default: "" },
        type: { type: String, default: "text" },
        placeholder: { type: String, default: "" },
        modelValue: { type: String, default: "" },
        error: {
            type: String,
            default: "",
        },
    },
};
</script>
