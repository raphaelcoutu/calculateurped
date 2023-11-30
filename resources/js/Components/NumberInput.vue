<script setup>
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        required: false
    },
    suffix: {
        type: String,
        required: false
    },
    type: {
        type: String,
        required: false,
        default: 'text'
    },
    min: {
        type: Number,
        required: false
    },
    max: {
        type: Number,
        required: false
    }
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)
onMounted(() => {
    if (input.value.hasAttributes('autofocus')) {
        input.value.focus();
    }
})
defineExpose({focus: () => input.value.focus()})

watch(() => props.modelValue, (newVal) => {
    inputValue.value = newVal
})

const inputValue = ref('')
const handleInput = () => {
    // Remove non-digit characters except dots and commas
    inputValue.value = inputValue.value.replace(/[^\d.,]/g, '')

    // Replace commas with dots
    inputValue.value = inputValue.value.replace(/,/g, '.');

    // Limit the input to two decimal places
    const parts = inputValue.value.split('.');
    if (parts.length > 1) {
        inputValue.value = `${parts[0]}.${parts[1].slice(0, 2)}`;
    }

    error.value = ''
    if(props.min && inputValue.value < props.min || props.max && inputValue.value > props.max) {
        error.value = `Veuillez inscrire un nombre entre ${props.min} et ${props.max}.`
    }

    emit('update:modelValue', inputValue.value)
}

const error = ref('')
</script>

<template>
    <div>
        <div class="flex items-center relative">
            <input
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm w-full block"
                :class="{'rounded-r-none': $props.suffix}"
                v-model="inputValue"
                @input="handleInput"
                ref="input"
                :min="$props.min"
                :max="$props.max"
                type="text"
                :inputmode="type === 'number' ? 'decimal' : null"
            >
            <p v-if="$props.suffix"
               class="py-2.5 px-2.5 border border-l-0 rounded rounded-l-none border-gray-300 text-sm font-semibold">{{
                    $props.suffix
                }}</p>
        </div>
        <small v-if="error" class="text-red-500">{{ error }}</small>
    </div>
</template>
