<template>
    <Tabs>
        <Tab name="Kilos" id="kilogram" class="pt-3">
            <Kilogram></Kilogram>
        </Tab>
        <Tab name="Livres" id="pounds" class="pt-3">
            <Pound></Pound>
        </Tab>
        <Tab name="Âge/Sexe" id="agesex" class="pt-3">
            <AgeSex></AgeSex>
        </Tab>
        <Tab name="Broselow" id="broselow" class="pt-3">
            <Broselow></Broselow>
        </Tab>
    </Tabs>

</template>

<script>
    import Tab from './Tab.vue'
    import Tabs from './Tabs.vue'
    import Kilogram from './tabs/Kilogram.vue'
    import Pound from './tabs/Pound.vue'
    import AgeSex from './tabs/AgeSex.vue'
    import Broselow from "./tabs/Broselow.vue";

    export default {
        components: {
            Tabs, Tab,
            Kilogram, Pound, AgeSex, Broselow
        }
    }


</script>
