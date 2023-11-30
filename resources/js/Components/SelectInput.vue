<script setup>
defineProps(['modelValue'])
defineEmits(['update:modelValue'])
</script>

<template>
    <select class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm w-full block"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)">
        <slot/>
    </select>
</template>

<style scoped>

</style>
