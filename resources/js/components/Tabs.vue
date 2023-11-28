<template>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li v-for="(tab, i) in tabs"
                :key="i"
                role="presentation"
                class="nav-item"
            >
                <a v-html="tab.name"
                   @click.prevent="selectTab(tab.hash)"
                   href="#"
                   class="nav-link"
                   :class="{ 'active' : tab.isActive }"
                ></a>
            </li>
        </ul>
        <div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['activeTab'],

        data: () => ({
            tabs: [],
            activeTabHash: ''
        }),

        created() {
            this.tabs = this.$children
            this.activeTabHash = this.activeTab
        },

        mounted() {
            if(this.findTab(this.activeTabHash)) {
                this.selectTab(this.activeTabHash)
            } else {
                this.selectTab(this.tabs[0].hash)
            }
        },

        methods: {
            selectTab(tabHash) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.hash === tabHash)
                })

                this.activeTabHash = tabHash
            },

            findTab(hash) {
                return this.tabs.find(tab => tab.hash === hash)
            }
        }
    }

</script>