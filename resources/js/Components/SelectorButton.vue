<script setup>
defineEmits(['click'])
</script>

<template>
    <button type="button"
            class="button" @click="$emit('click')">
        <slot />
    </button>
</template>

<style>
.button {
    @apply bg-blue-50 border border-blue-200 w-32 rounded py-2 md:py-4 text-sm md:text-base text-blue-900 hover:bg-blue-100 active:bg-blue-200 shadow dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 dark:active:bg-gray-500;
}

/*noinspection CssUnusedSymbol*/
.active {
    @apply border-2 border-blue-500 font-semibold dark:border-blue-400;
}
</style>
