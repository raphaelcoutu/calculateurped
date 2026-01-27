<script setup>
defineProps({
    label: {
        type: String
    }, modelValue: {
        type: Boolean
    }
})
defineEmits(['update:model-value'])
</script>

<template>
    <div class="flex items-center">
        <input type="checkbox"
               id="isWeightEstimated"
               @change="$emit('update:model-value', $event.target.checked)"
               :checked="modelValue"
               class="relative w-[3.25rem] h-7 p-px bg-gray-200 border-transparent text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:ring-indigo-600 disabled:opacity-50 disabled:pointer-events-none checked:bg-none checked:text-indigo-600 checked:border-indigo-600 focus:checked:border-indigo-600 before:inline-block before:w-6 before:h-6 before:bg-white checked:before:bg-indigo-200 before:translate-x-0 checked:before:translate-x-full before:rounded-full before:shadow before:transform before:ring-0 before:transition before:ease-in-out before:duration-200 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700">
        <label for="isWeightEstimated"
               class="text-sm ms-3 cursor-pointer dark:text-gray-300">{{ label }}</label>
    </div>
</template>
