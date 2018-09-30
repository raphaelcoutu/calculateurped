<template>
    <section v-if="isActive"
             :id="hash"
             role="tabpanel"
    >
        <slot></slot>
    </section>
</template>

<script>
    export default {
        props: ['name', 'id'],

        data: () => ({
            isActive: false
        }),

        computed: {
            hash() {
                return this.id ?
                    this.id :
                    this.name.toLowerCase().replace('/ /g', '-')
            }
        }
    }
</script>