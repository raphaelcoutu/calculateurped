<script setup>
import { ref } from "vue";
import InputLabel from "./InputLabel.vue";
import SelectInput from "./SelectInput.vue";
import AgeSexHelper from "../helpers/AgeSexHelper";

const emit = defineEmits(['change'])

const sex = ref(null);
const age = ref(null);

const emitChange = () => {
    if (sex === null || age === null) return 0;
    let weight = AgeSexHelper.get(age.value, sex.value);
    emit('change', {value: weight, estimated: true})
}

defineExpose({
    reset: () => {
        sex.value = null;
        age.value = null;
    }
})
</script>

<template>
    <div>
        <div>
            <InputLabel>Sexe</InputLabel>
            <SelectInput
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm w-full block dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                v-model="sex" @update:modelValue="emitChange">
                <option selected disabled value="null">Sélectionnez...</option>
                <option value="male">Masculin</option>
                <option value="female">Féminin</option>
            </SelectInput>
        </div>
        <div class="mt-4">
            <InputLabel>Âge</InputLabel>
            <SelectInput
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-600 rounded-md shadow-sm w-full block dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                v-model="age" @update:modelValue="emitChange">
                <option selected disabled value="null">Sélectionnez...</option>
                <option value="month_1">1 mois</option>
                <option value="month_2">2 mois</option>
                <option value="month_3">3 mois</option>
                <option value="month_4">4 mois</option>
                <option value="month_5">5 mois</option>
                <option value="month_6">6 mois</option>
                <option value="month_7">7 mois</option>
                <option value="month_8">8 mois</option>
                <option value="month_9">9 mois</option>
                <option value="month_10">10 mois</option>
                <option value="month_11">11 mois</option>
                <option value="month_12">12 mois</option>
                <option value="month_13">13 mois</option>
                <option value="month_14">14 mois</option>
                <option value="month_15">15 mois</option>
                <option value="month_16">16 mois</option>
                <option value="month_17">17 mois</option>
                <option value="month_18">18 mois</option>
                <option value="month_19">19 mois</option>
                <option value="month_20">20 mois</option>
                <option value="month_21">21 mois</option>
                <option value="month_22">22 mois</option>
                <option value="month_23">23 mois</option>
                <option value="month_24">24 mois</option>
                <option value="year_3">3 ans</option>
                <option value="year_4">4 ans</option>
                <option value="year_5">5 ans</option>
                <option value="year_6">6 ans</option>
                <option value="year_7">7 ans</option>
                <option value="year_8">8 ans</option>
                <option value="year_9">9 ans</option>
                <option value="year_10">10 ans</option>
                <option value="year_11">11 ans</option>
                <option value="year_12">12 ans</option>
                <option value="year_13">13 ans</option>
                <option value="year_14">14 ans</option>
                <option value="year_15">15 ans</option>
                <option value="year_16">16 ans</option>
                <option value="year_17">17 ans</option>
                <option value="year_18">18 ans</option>
            </SelectInput>
        </div>
    </div>
</template>
