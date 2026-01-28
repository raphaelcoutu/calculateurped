<script setup>
import SelectorButton from "./SelectorButton.vue";
import { ref } from "vue";
import Kilogram from "./Kilogram.vue";
import Pound from "./Pound.vue";
import AgeSex from "./AgeSex.vue";
import Broselow from "./Broselow.vue";

const active = ref('kg');

const weight = ref({
    value: 0,
    estimated: false
});

const changeActive = (value) => {
    // Prevent refresh if already active
    if(value === active.value) return

    active.value = value;
    weight.value = 0;
}

const onChange = (value) => {
    weight.value = value;
}

const activeRef = ref()

const reset = () => {
    activeRef.value.reset();
}

</script>

<template>
    <div class="flex justify-around mt-10 mb-4 md:w-3/4 mx-auto text-center items-center text-xl space-x-2">
        <SelectorButton :class="{'active': active === 'kg'}" @click="changeActive('kg')">Kilogrammes</SelectorButton>
        <SelectorButton :class="{'active': active === 'pound'}" @click="changeActive('pound')">Livres</SelectorButton>
        <SelectorButton :class="{'active': active === 'agesex'}" @click="changeActive('agesex')">Âge/Sexe
        </SelectorButton>
        <SelectorButton :class="{'active': active === 'broselow'}" @click="changeActive('broselow')">Broselow
        </SelectorButton>
    </div>

    <Kilogram v-if="active === 'kg'" ref="activeRef" @change="onChange"/>
    <Pound v-if="active === 'pound'" ref="activeRef" @change="onChange"/>
    <AgeSex v-if="active === 'agesex'" ref="activeRef" @change="onChange"/>
    <Broselow v-if="active === 'broselow'" ref="activeRef" @change="onChange"/>

    <input type="hidden" name="weight" :value="weight.value">
    <input type="hidden" name="estimated" :value="weight.estimated">

    <div class="mt-5 space-x-2">
        <button type="submit" class="border rounded py-2 px-3 bg-blue-600 text-white disabled:cursor-not-allowed disabled:bg-blue-300 dark:bg-blue-800 dark:disabled:bg-blue-900" :disabled="!weight.value">Calculer</button>
        <button type="button" @click="reset" class="border border-blue-500 rounded py-2 px-3 bg-blue-50 text-blue-900 dark:bg-gray-700 dark:text-white dark:border-gray-600">Mise à
            zéro
        </button>
    </div>
</template>
