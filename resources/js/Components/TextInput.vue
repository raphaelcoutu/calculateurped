<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: {
        type: String,
        required: false
    },
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttributes('autofocus')) {
        input.value.focus();
    }
})

defineExpose({focus: () => input.value.focus()})
</script>

<template>
    <input
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm w-full block"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        type="text"
    >
</template>
