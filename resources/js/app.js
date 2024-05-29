"use strict";

require('es6-promise').polyfill();
import 'core-js/stable';
import 'regenerator-runtime/runtime';
// import 'es6-promise/auto';
// import route from '../../vendor/tightenco/ziggy/dist';
import route from '../../vendor/tightenco/ziggy/src/js';
import Echo from 'laravel-echo';
import { Ziggy } from './ziggy';
import moment from 'moment';
import lodash from 'lodash';
import VueLodash from 'vue-lodash';
import VueSweetalert2 from 'vue-sweetalert2';
import DropdownMenu from '@innologica/vue-dropdown-menu';
import vSelect from 'vue-select';
import Donut from 'vue-css-donut-chart';
import LaravelVuePagination from 'laravel-vue-pagination';
import Form from 'vform';
import { Button, HasError, AlertError, AlertErrors, AlertSuccess } from 'vform/src/components/bootstrap4';
import { VTooltip, VPopover, VClosePopover } from 'v-tooltip';
import Vue2Editor from 'vue2-editor';
import Autocomplete from '@trevoreyre/autocomplete-vue';
import Storage from 'vue-ls';
import { mapActions, mapGetters } from 'vuex';
import store from './vuex/store';
import VueCookies from 'vue-cookies';
import VueCountdown from '@chenfengyuan/vue-countdown';

import './plugins';

window.Vue = require('vue').default;
window.collect = require('collect.js');
window.axios = require('axios');
window.io = require('socket.io-client');
window.route = route;
window.Ziggy = Ziggy;
window.moment = moment;
window.Form = Form;

VTooltip.options.autoHide = true;
VTooltip.options.trigger = 'click';
VTooltip.options.hideOnTargetClick = true;
VTooltip.options.placement = 'top';

window.axios.defaults.withCredentials = true;

window.axios.defaults.headers.common = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': '*',
    'crossDomain': true,
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').getAttribute('content'),
};

window.axios.interceptors.response.use(
    response => response,
    error => {
        if( error.response )
        {
            switch (error.response.status)
            {
                case 419:
                    window.location.reload();
                    return null;
                break;
                default:
                break;
            }
        }
        return Promise.reject(error);
    }
);


// window.axios.interceptors.response.use((response) => response, (error) => {
//     var vm = this;
//     if( error.response.data.message == 'Unauthenticated.' )
//     {
//         vm.VuexSignOut().then(() => {
//             window.location.href = route('home');
//         });
//     }
//     throw error;
// });


Vue.mixin({
    mounted()
    {
        var vm = this;

        vm.disableAutoComplete();
        
        // if( vm.isAuthenticated && !window.isAuthenticated )
        // {
        //     vm.$nextTick(() => {
        //         vm.VuexSignOut().then(() => {
        //             window.location.href = route('home');
        //         });
        //     });
        // }
        
        if( !vm.isAuthenticated && window.isAuthenticated || vm.isAuthenticated && !window.isAuthenticated )
        {
            vm.$nextTick(() => {
                vm.VuexSignIn()
                    .then(() => {
                        vm.VuexGetSteps()
                            .then(() => {
                                window.location.reload();
                            });
                    })
                    .catch(() => {
                        vm.VuexUnSetSteps().then(() => {
                            vm.VuexSignOut().then(() => {
                                window.location.reload();
                            });
                        });
                    });
            });
        }

        if( window.isAuthenticated && window.huid )
        {
            if( window.huid != vm.authUser.slug )
            {
                vm.$nextTick(() => {
                    vm.VuexSignIn()
                        .then(() => {
                            vm.VuexGetSteps()
                                .then(() => {
                                    window.location.reload();
                                });
                        })
                        .catch(() => {});
                });
            }
        }
    },
    computed: {
        ...mapGetters({
            isAuthenticated: 'auth/isAuthenticated',
            authUser: 'auth/authUser',
            vsteps: 'steps/steps',
        }),
        URLprevious()
        {
            return window.URLprevious;
        },
        URLcurrent()
        {
            return window.URLcurrent;
        },
        usessionid()
        {
            var vm = this;
            return window.suid;
        }
    },
    created()
    {
        var vm = this;
    },
    filters: {
        StrLimit: function(value, limit, end)
        {
            if( !limit )
            {
                limit = 20;
            }

            if( !end )
            {
                end = '...';
            }
            
            if( value && value.length > limit )
            {
                value = value.substring(0, limit) + end;
            }

            return value;
        }
    },
    methods: {
        ...mapActions({
            VuexSignIn: 'auth/login',
            VuexSignOut: 'auth/logout',
            VuexGetSteps: 'steps/setSteps',
            VuexUnSetSteps: 'steps/unsetSteps',
        }),
        // route: function ( name, params, absolute ) { return route(name, params, absolute, Ziggy); },
        // if you prefer ES6 syntax
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),
        moment(params, format)
        {
            var vm = this;

            if( !vm.lodash.isEmpty(format) )
            {
                return moment(params, format).locale('ru');
            }

            return moment(params).locale('ru');
        },
        temporarySeniority(enter_date)
        {
            var vm = this,
                now = moment(),
                enter = moment(enter_date, "YYYY-MM-DD"),
                duration = moment.duration(now.diff(enter)),
                years = duration.get('years'),
                months = duration.get('months'),
                days = duration.get('days'),
                d_result = ( months == 0 ? ( days > 0 ? days + ' ' + vm.numeralDeclension(days, ['день', 'дня', 'дней']) : '' )  : '' ),
                m_result = ( months > 0 ? months + ' ' + vm.numeralDeclension(months, ['месяц', 'месяца', 'месяцев']) : '' ),
                y_result = ( years > 0 ? years + ' ' + vm.numeralDeclension(years, ['год', 'года', 'лет']) : '' );

            return years > 0 ? y_result + ( !vm.lodash.isEmpty(m_result) || !vm.lodash.isEmpty(d_result) ? ', ' + m_result + d_result : '' ) : m_result + d_result;
        },
        numeralDeclension(number, titles)
        {
            var vm = this,
                number = Math.abs(number),
                cases = [2, 0, 1, 1, 1, 2];
            
            if( Number.isInteger(number) )
            {
                return titles[ (number%100>4 && number%100<20) ? 2 : cases[(number%10<5)?number%10:5] ];
            }
            return titles[1];
        },
        collect(arr)
        {
            return collect(arr);
        },
        disableAutoComplete()
        {
            var vm = this,
                elements = document.querySelectorAll('[autocomplete="off"]:not(.vs__search):not(.form-field__search)');
            
            if( !elements )
            {
                return;
            }

            elements.forEach((element) => {

                element.setAttribute('readonly', 'readonly');

                element.onfocus = () => {

                    element.removeAttribute('readonly');

                }

            });
        },
        asset(path)
        {
            var vm = this,
                prefix = process.env.MIX_ASSET_URL;

            if( !prefix )
            {
                prefix = document.head.querySelector('meta[name="asset-url"]').content;
            }
            
            return prefix.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
        },
        objectToUrlQuery(queryObject)
        {
            var vm = this,
                queryString = '';

            Object.keys(queryObject).forEach(key => {
                queryString += `${key}=${encodeURI(queryObject[key])}&`;
            });

            if( queryString )
            {
                return queryString.slice(0, -1);
            }
            
            return queryString;
        },
        ArrayQueueСheck(arr, prev = null)
        {
            var vm = this;
            
            if( !collect(arr).count() )
            {
                return true;
            }

            var current = collect(arr).shift();

            if( prev == null || current === ( prev + 1 ) )
            {
                return vm.ArrayQueueСheck(collect(arr).all(), current);
            }

            return false;
        }
    }
});

const files = require.context('./', true, /\.vue$/i);

files.keys().map((key) => {
    var namesArray = key.split('/'),
        NewName = '';
    namesArray.forEach(function(item, i){
        if( i > 1 )
        {
            NewName = NewName + (item.charAt(0).toUpperCase() + item.slice(1));
        }
    });
    return Vue.component(NewName.split('/').pop().split('.')[0], files(key).default);
});

Vue.use(VueLodash, { lodash: lodash });
Vue.use(VueSweetalert2);
Vue.use(Donut);
Vue.use(Vue2Editor);
Vue.use(Autocomplete);
Vue.use(Storage);
Vue.use(VueCookies, {
    expire: '365d'
});

Vue.directive('tooltip', VTooltip);
Vue.directive('close-popover', VClosePopover);

Vue.component('v-select', vSelect);
Vue.component('vFormButton', Button);
Vue.component('vFormHasError', HasError);
Vue.component('vFormAlertError', AlertError);
Vue.component('vFormAlertErrors', AlertErrors);
Vue.component('vFormAlertSuccess', AlertSuccess);
Vue.component('dropdown-menu', DropdownMenu);
Vue.component('v-popover', VPopover);
Vue.component('LaravelVuePagination', LaravelVuePagination);
Vue.component('VueCountdown', VueCountdown);

const app = new Vue({
    el: '#app',
    store: store
});