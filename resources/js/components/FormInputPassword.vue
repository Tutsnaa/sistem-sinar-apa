<template>
    <div class="mb-4">
        <label :for="id" class="block text-gray-700 mb-2">{{ label }}</label>
        <div class="relative">
            <input
                :id="id"
                :type="showPassword ? 'text' : 'password'"
                :placeholder="placeholder"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
            />
            <span
                class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500"
                @click="toggleShowPassword"
            >
                <span class="material-icons">
                    {{ showPassword ? "visibility_off" : "visibility" }}
                </span>
            </span>
        </div>
        <!-- PESAN ERROR -->
        <p v-if="error" class="text-red-500 text-sm mt-1">
            {{ error }}
        </p>
    </div>
</template>

<script>
export default {
    name: "FormInputPassword",
    props: {
        modelValue: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: "Kata Sandi",
        },
        placeholder: {
            type: String,
            default: "",
        },
        error: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            showPassword: false,
        };
    },
    methods: {
        toggleShowPassword() {
            this.showPassword = !this.showPassword;
        },
    },
};
</script>
