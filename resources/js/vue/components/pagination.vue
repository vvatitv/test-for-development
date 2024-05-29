<template>
    <div class="pagination-main-container" v-if="!lodash.isEmpty(data) && ( data.total > data.per_page )">
        <LaravelVuePagination
            :data="data"
            :limit="limit"
            :show-disabled="showDisabled"
            :size="size"
            :align="align"
            @pagination-change-page="onPaginationChangePage"
        >
            <template #prev-nav>
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="12" viewBox="0 0 6 12"><path d="M5.39,12a.61.61,0,0,1-.45-.2L.17,6.44a.65.65,0,0,1,0-.88L4.94.21a.58.58,0,0,1,.86,0,.66.66,0,0,1,0,.91L1.45,6l4.38,4.92a.66.66,0,0,1,0,.91A.58.58,0,0,1,5.39,12Z" /></svg>
                </span>
            </template>
            <template #next-nav>
                <span class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="12" viewBox="0 0 6 12"><path d="M.58,12a.53.53,0,0,1-.41-.19.69.69,0,0,1,0-.91L4.6,6,.17,1.1a.69.69,0,0,1,0-.91A.54.54,0,0,1,1,.19L5.83,5.55a.67.67,0,0,1,0,.9L1,11.81A.53.53,0,0,1,.58,12Z"/></svg>
                </span>
            </template>
        </LaravelVuePagination>
        <div class="note" v-if="!hideNote">
            <slot name="note">
                <span>Показано <span class="--is-show">{{ data.from }}-{{ data.to }}</span> из <span class="--total">{{ data.total }}</span></span>
            </slot>
        </div>
    </div>
</template>
<script>
export default {
    inheritAttrs: false,
    emits: ['pagination-change-page'],
    props: {
        data: {
            type: Object,
            default: () => {}
        },
        limit: {
            type: Number,
            default: 2
        },
        showDisabled: {
            type: Boolean,
            default: true
        },
        size: {
            type: String,
            default: 'small',
            validator: value => {
                return ['small', 'default', 'large'].indexOf(value) !== -1;
            }
        },
        align: {
            type: String,
            default: 'left',
            validator: value => {
                return ['left', 'center', 'right'].indexOf(value) !== -1;
            }
        },
        hideNote:{
            type: Boolean,
            default: false
        }
    },
    mounted()
    {
        var vm = this;
    },
    methods: {
        onPaginationChangePage(page)
        {
            var vm = this;
            vm.$emit('pagination-change-page', page);
        }
    }
}
</script>