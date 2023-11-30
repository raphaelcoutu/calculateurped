import { createApp } from "vue";
import InputLabel from "./Components/InputLabel.vue";
import TextInput from "./Components/TextInput.vue";
import Selector from "./Components/Selector.vue";

createApp({})
    .component('text-input', TextInput)
    .component('input-label', InputLabel)
    .component('selector', Selector)
    .mount('#app')
