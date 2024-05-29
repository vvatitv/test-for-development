import axios from 'axios';

export default {
    namespaced: true,
    state:{
        isAuthenticated: false,
        authUser: {}
    },
    getters:{
        isAuthenticated(state)
        {
            return state.isAuthenticated
        },
        authUser(state)
        {
            return state.authUser
        }
    },
    mutations:{
        SET_AUTHENTICATED (state, value)
        {
            state.isAuthenticated = value
        },
        SET_USER (state, value)
        {
            state.authUser = value
        }
    },
    actions:{
        login({commit})
        {
            return new Promise((resolve, reject) => {

                axios
                    .get(route('api.user.index'), {
                        params:{
                            with:[
                                'all',
                            ],
                            appends:[
                                'team',
                            ]
                        }
                    })
                    .then((response) => {
                        commit('SET_USER', response.data.data);
                        commit('SET_AUTHENTICATED', true);
                        resolve();
                    }).catch((error) => {
                        console.log('auth error', error);
                        commit('SET_USER', {});
                        commit('SET_AUTHENTICATED', false);
                        reject(error);
                    });
            });
        },
        logout({commit})
        {
            return new Promise((resolve, reject) => {
                commit('SET_USER', {});
                commit('SET_AUTHENTICATED', false);
                resolve();
            });
        }
    }
}