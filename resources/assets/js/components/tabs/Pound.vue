<template>
    <div>
        <div class="row">
            <div class="col-6 form-group">
                <label>Inscrire le poids en <strong>livres</strong>:</label>
                <input type="text" class="form-control" maxlength="5" v-model="input" number>
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
        }
    }

</script>