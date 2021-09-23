import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);

const vuetify = new Vuetify({
    theme: {
        themes: {
            dark: {
                primary: '#00796B',
            },
            light: {
                primary: '#00796B',
            }
        }
    },
})

export default vuetify
