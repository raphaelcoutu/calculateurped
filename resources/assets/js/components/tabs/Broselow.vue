<template>
    <div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Échelle de Broselow:</label>
                    <select class="form-control" v-model="broselow">
                        <option selected disabled value="null">Sélectionnez...</option>
                        <option value="grey">Gris</option>
                        <option value="pink">Rose</option>
                        <option value="red">Rouge</option>
                        <option value="purple">Mauve</option>
                        <option value="yellow">Jaune</option>
                        <option value="white">Blanc</option>
                        <option value="blue">Bleu</option>
                        <option value="orange">Orange</option>
                        <option value="green">Vert</option>
                    </select>
                </div>
            </div>


            <div class="col-6 col-sm-3" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à :</span>
                <h2>{{ result }} kg</h2>
            </div>
            <div class="col-8 col-sm-3 border border-dark broselow-box mx-auto m-sm-0 mb-3" :class="bgColor" v-show="result"></div>
        </div>

        <div class="d-flex">
            <input type="hidden" name="weight" :value="result">
            <button type="submit"
                    class="btn btn-primary px-4"
                    :class="{ 'disabled' : !result }"
                    :disabled="!result"
            >Calculer</button>
            <a href="/" class="btn btn-danger px-4 ml-4">Mise à zéro</a>
        </div>
    </div>
</template>

<script>
    import BroselowHelper from '../../helpers/BroselowHelper'

    export default {
        data: () => ({
            broselow: null
        }),

        computed: {
            result() {
                return (this.broselow) ? BroselowHelper.get(this.broselow) : null
            },
            bgColor() {
                return (this.broselow) ? 'broselow-' + this.broselow : null
            }
        }
    }
</script>