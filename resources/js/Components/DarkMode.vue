<script setup>
import { useDark, useToggle } from "@vueuse/core";
import Light from '@carbon/icons-vue/es/light/32'
import Dark from '@carbon/icons-vue/es/light--filled/32'

const isDark = useDark()
const toggleDark = useToggle(isDark)
</script>

<template>
    <div>
        <button @click="toggleDark()">
            <Light v-if="isDark" class="text-gray-500"/>
            <Dark v-else class="text-gray-500"/>
        </button>
    </div>
</template>
