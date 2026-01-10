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
                @focus="$emit('focus')"
                @blur="$emit('blur')"
                :class="[
                    'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2',
                    error
                        ? 'border-red-500 focus:ring-red-300'
                        : 'border-gray-300 focus:ring-blue-400',
                ]"
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
