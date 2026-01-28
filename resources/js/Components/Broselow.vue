<script setup>
import { ref } from "vue";
import InputLabel from "./InputLabel.vue";
import SelectInput from "./SelectInput.vue";
import BroselowHelper from "../helpers/BroselowHelper";

const emit = defineEmits(['change'])

const color = ref(null)

const emitChange = () => {
    if (color === null) return 0;
    let weight = BroselowHelper.get(color.value);
    emit('change', {value: weight, estimated: true})
}

defineExpose({
    reset: () => {
        color.value = null;
    }
})
</script>

<template>
    <div>
        <InputLabel>Échelle de Broselow</InputLabel>
        <SelectInput v-model="color" @update:modelValue="emitChange" class="dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option selected disabled value="null">Sélectionnez...</option>
            <option value="grey" class="bg-gray-400 dark:bg-gray-600">Gris</option>
            <option value="pink" class="bg-pink-400 dark:bg-pink-800">Rose</option>
            <option value="red" class="bg-red-400 dark:bg-red-800">Rouge</option>
            <option value="purple" class="bg-purple-400 dark:bg-purple-800">Mauve</option>
            <option value="yellow" class="bg-yellow-400 dark:bg-yellow-700">Jaune</option>
            <option value="white">Blanc</option>
            <option value="blue" class="bg-blue-400 dark:bg-blue-800">Bleu</option>
            <option value="orange" class="bg-orange-400 dark:bg-orange-800">Orange</option>
            <option value="green" class="bg-green-400 dark:bg-green-800">Vert</option>
        </SelectInput>
    </div>
</template>
