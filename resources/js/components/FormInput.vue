<template>
    <div class="mb-4">
        <label :for="id" class="block text-gray-700 mb-1">
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
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
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
    },
};
</script>
