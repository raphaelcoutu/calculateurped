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


            <div class="col-md-4" v-show="result">
                <span class="text-uppercase text-muted font-weight-bold">Correspond à :</span>
                <h2>{{ result }} kg</h2>
            </div>
            <div class="col-md-2 col-5 border border-dark mt-1 mb-3" :class="bgColor" v-show="result"></div>
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