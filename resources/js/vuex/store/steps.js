import axios from 'axios';

export default {
    namespaced: true,
    state:{
        steps: {}
    },
    getters:{
        steps(state)
        {
            return state.steps
        }
    },
    mutations:{
        SET_STEPS (state, value)
        {
            state.steps = value
        }
    },
    actions:{
        setSteps({commit})
        {
            return new Promise((resolve, reject) => {
                axios
                    .get(route('api.step.index'), {
                        params:{
                            with:[
                                'votes'
                            ],
                            appends:[]
                        }
                    })
                    .then((response) => {
                        commit('SET_STEPS', response.data.data);
                        resolve();
                    }).catch((error) => {
                        console.log('settings error', error);
                        commit('SET_STEPS', {});
                        reject(error);
                    });
            });
        },
        unsetSteps({commit})
        {
            return new Promise((resolve, reject) => {
                commit('SET_STEPS', {});
                resolve();
            });
        }
    }
}