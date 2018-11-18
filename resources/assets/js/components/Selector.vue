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
    import Tab from './Tab'
    import Tabs from './Tabs'
    import Kilogram from './tabs/Kilogram'
    import Pound from './tabs/Pound'
    import AgeSex from './tabs/AgeSex'
    import Broselow from "./tabs/Broselow";

    export default {
        components: {
            Tabs, Tab,
            Kilogram, Pound, AgeSex, Broselow
        }
    }


</script>