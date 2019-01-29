<template>
    <div>
        <div class="row">
            <div class="col-6 form-group">
                <label>Inscrire le poids en <strong>livres</strong>:</label>
                <input type="text" class="form-control" maxlength="5" v-model="input" number>
            </div>
            <div class="col-6" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à:</span>
                <div v-if="!$v.input.between" class=" px-4 text-danger">Veuillez inscrire un nombre entre 4.4 et 550 lbs.</div>
                <h2 v-else>{{ result }} kg</h2>
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
    import { required, between } from 'vuelidate/lib/validators'

    export default {
        data: () => ({
            input: ''
        }),

        computed: {
            result() {
                if(this.input) {
                    return (this.input/2.2).toFixed(2)
                }
            }
        },

        validations: {
            input: {
                required,
                between: between(4.4, 550)
            }
        }

    }

</script>