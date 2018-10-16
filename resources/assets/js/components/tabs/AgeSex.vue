<template>
    <div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Sexe:</label>
                    <select class="form-control" v-model="sex">
                        <option selected disabled value="null">Sélectionnez...</option>
                        <option value="male">Masculin</option>
                        <option value="female">Féminin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Âge:</label>
                    <select class="form-control" v-model="age">
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
                    </select>
                </div>
            </div>
            <div class="col-6" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à:</span>
                <h2>{{ result }} kg</h2>
            </div>
        </div>

        <div class="d-flex">
            <input type="hidden" name="weight" :value="result">
            <button type="submit"
                    class="btn btn-primary px-4"
                    :class="{ 'disabled' : !result }"
                    :disabled="!result"
            >Calculer</button>
            <a href="/reset" class="btn btn-danger px-4 ml-4">Mise à zéro</a>
        </div>
    </div>
</template>

<script>
    import AgeSexHelper from "../../helpers/AgeSexHelper";

    export default {
        data: () => ({
            age: null,
            sex: null
        }),

        computed: {
            result() {
                if(this.sex && this.age) {
                    return AgeSexHelper.get(this.age, this.sex)
                }
            }
        }
    }

</script>