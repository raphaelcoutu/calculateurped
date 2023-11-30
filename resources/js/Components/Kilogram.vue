<script setup>
import InputLabel from "./InputLabel.vue";
import Checkbox from "./Checkbox.vue";
import { ref } from "vue";
import NumberInput from "./NumberInput.vue";

const emit = defineEmits(['change'])

const input = ref(null)
const checkbox = ref(false)
const inputChanged = (value) => {
    input.value = value;
    emit('change', {value: input.value, estimated: checkbox.value})
}

const checkboxChanged = (value) => {
    checkbox.value = value
    emit('change', {value: input.value, estimated: checkbox.value})
}

defineExpose({
    reset: () => {
        input.value = ''
        checkbox.value = false
    }
})

</script>

<template>
    <div>
        <div>
            <InputLabel>Poids en kilogrammes</InputLabel>
            <NumberInput :min=2 :max=250 suffix="kg" :model-value="input"
                         @update:modelValue="inputChanged"/>
        </div>
        <div class="mt-2">
            <Checkbox label="Poids estimé" :model-value="checkbox" @update:modelValue="checkboxChanged"/>
        </div>
    </div>
</template>
