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
    @apply bg-indigo-50 border border-indigo-500 w-32 rounded py-2 md:py-4 text-sm md:text-base text-indigo-900 hover:bg-indigo-600 hover:text-indigo-100 active:bg-indigo-700 active:text-indigo-100 shadow;
}

/*noinspection CssUnusedSymbol*/
.active {
    @apply bg-indigo-500 text-indigo-100;
}
</style>
