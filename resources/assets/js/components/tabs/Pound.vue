<template>
    <div>
        <div class="row">
            <div class="col-md-6 col-xs-12 form-group">
                <label>Inscrire le poids en <strong>livres</strong>:</label>
                <input type="text" class="form-control" maxlength="5" v-model="input" number>
            </div>
            <div class="col-md-6" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à:</span>
                <h2>{{ result }} kg</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <input type="hidden" name="weight" :value="result">
                <button type="submit"
                        class="btn btn-primary btn-block"
                        :class="{ 'disabled' : !result }"
                        :disabled="!result"
                >Calculer</button>
            </div>
            <div class="col-3">
                <button class="btn btn-danger btn-block">Mise à zéro</button>
            </div>
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