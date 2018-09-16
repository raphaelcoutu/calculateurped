<template>
    <div>
        <tabs @changed="selectedTabChanged">
            <tab name="Kilos" id="kilos">
                <input type="text" name="kilos" v-model="kilos">
            </tab>
            <tab name="Livres" id="livres">
                <input type="text" name="livres" v-model="livres">
            </tab>
            <tab name="Âge/Sexe" id="age-sexe">
                <label>Âge:</label>
                <select name="age" class="form-control" v-model="age">
                    <option selected disabled>--- Choisir ---</option>
                    <option value="mois_1">1 mois</option>
                    <option value="mois_2">2 mois</option>
                    <option value="mois_3">3 mois</option>
                    <option value="mois_4">4 mois</option>
                    <option value="mois_5">5 mois</option>
                    <option value="mois_6">6 mois</option>
                    <option value="mois_7">7 mois</option>
                    <option value="mois_8">8 mois</option>
                    <option value="mois_9">9 mois</option>
                    <option value="mois_10">10 mois</option>
                    <option value="mois_11">11 mois</option>
                    <option value="mois_12">12 mois</option>
                    <option value="mois_13">13 mois</option>
                    <option value="mois_14">14 mois</option>
                    <option value="mois_15">15 mois</option>
                    <option value="mois_16">16 mois</option>
                    <option value="mois_17">17 mois</option>
                    <option value="mois_18">18 mois</option>
                    <option value="mois_19">19 mois</option>
                    <option value="mois_20">20 mois</option>
                    <option value="mois_21">21 mois</option>
                    <option value="mois_22">22 mois</option>
                    <option value="mois_23">23 mois</option>
                    <option value="mois_24">24 mois</option>
                    <option value="ans_3">3 ans</option>
                    <option value="ans_4">4 ans</option>
                    <option value="ans_5">5 ans</option>
                    <option value="ans_6">6 ans</option>
                    <option value="ans_7">7 ans</option>
                    <option value="ans_8">8 ans</option>
                    <option value="ans_9">9 ans</option>
                    <option value="ans_10">10 ans</option>
                    <option value="ans_11">11 ans</option>
                    <option value="ans_12">12 ans</option>
                    <option value="ans_13">13 ans</option>
                    <option value="ans_14">14 ans</option>
                    <option value="ans_15">15 ans</option>
                    <option value="ans_16">16 ans</option>
                    <option value="ans_17">17 ans</option>
                    <option value="ans_18">18 ans</option>
                </select>
                <br>
                <label>
                    <input type="radio" name="sexe" value="m" v-model="sexe"> Masculin
                </label>
                <label>
                    <input type="radio" name="sexe" value="f" v-model="sexe"> Féminin
                </label>
            </tab>
            <tab name="Broselow" id="broselow">
                <select name="broselow" class="form-control" v-model="couleur">
                    <option disabled selected>--- Choisir ---</option>
                    <option value="gris">Gris</option>
                    <option value="rose">Rose</option>
                    <option value="rouge">Rouge</option>
                    <option value="mauve">Mauve</option>
                    <option value="jaune">Jaune</option>
                    <option value="blanc">Blanc</option>
                    <option value="bleu">Bleu</option>
                    <option value="orange">Orange</option>
                    <option value="vert">Vert</option>
                </select>
            </tab>
            <input type="hidden" name="weight" :value="weight">
        </tabs>
    </div>
</template>

<script>
    import ageSexe from '../helpers/AgeSexe'
    import broselow from '../helpers/Broselow'

    export default {
        data() {
            return {
                kilos: null,
                livres: null,
                age:null,
                sexe: null,
                couleur: null,
                selectedTab: null
            }
        },
        methods: {
            selectedTabChanged(obj) {
                const storageKey = `vue-tabs-component.cache.${window.location.host}${window.location.pathname}`;

                const cached = JSON.parse(
                    localStorage.getItem(storageKey)
                );

                if (! cached) {
                    return null;
                }

                const expires = new Date(cached.expires);

                if (expires < new Date()) {
                    localStorage.removeItem(storageKey);
                    return null;
                }

                this.selectedTab = cached.value;
            },
        },
        computed: {
            weight() {
                let tab = this.selectedTab;

                if (tab === "#kilos") {
                    return this.kilos;
                } else if (tab === "#livres") {
                    if(!this.livres) return;
                    return (this.livres / 2.2).toFixed(2);
                } else if (tab === "#age-sexe") {
                    if(!this.sexe || !this.age) return null;
                    return ageSexe.get(this.age,this.sexe)
                } else if (tab === "#broselow") {
                    if(!this.couleur) return null;
                    return broselow.get(this.couleur)
                }

                return null;
            }
        }
    }
</script>