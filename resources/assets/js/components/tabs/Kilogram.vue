<template>
    <div>
        <div class="row">
            <div class="col-6 form-group">
                <label>Inscrire le poids en <strong>kilogrammes</strong>:</label>
                <input type="text" class="form-control" maxlength="5" v-model.trim="$v.input.$model" number>
            </div>
            <div class="col-6" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à:</span>
                <div v-if="!$v.input.between" class=" px-4 text-danger">Veuillez inscrire un nombre entre 2 et 250 kg.</div>
                <h2 v-else="">{{ result }} kg</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-6 d-flex align-items-center ml-4">
                <input type="checkbox" name="isWeightEstimated" class="form-check-input" id="isWeightEstimated">
                <label class="form-check-label" for="isWeightEstimated">Poids estimé</label>
            </div>
        </div>

        <div class="d-flex mt-2">
            <input type="hidden" name="weight" :value="result">
            <button type="submit"
                    class="btn btn-primary px-4"
                    :class="{ 'disabled' : !result }"
                    :disabled="$v.input.$invalid && $v.input.$dirty"
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
                return this.input
            }
        },
        validations: {
            input: {
                required,
                between: between(2, 250)
            }
        }
    }

</script>
