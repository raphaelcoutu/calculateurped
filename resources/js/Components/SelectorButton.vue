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
    @apply bg-indigo-50 border border-indigo-200 w-32 rounded py-2 md:py-4 text-sm md:text-base text-indigo-900 hover:bg-indigo-100  active:bg-indigo-200 shadow;
}

/*noinspection CssUnusedSymbol*/
.active {
    @apply border-2 border-indigo-500 font-semibold;
}
</style>
