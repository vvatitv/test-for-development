<template>
    <div class="loader-main-container">
        <div class="loading-container" :style="size">
            <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
        </div>
    </div>
</template>
<script>
    export default {
        props: [
            'width',
            'height',
        ],
        data(){
            return {
                size: ''
            }
        },
        mounted()
        {
            var vm = this;

            if( vm.width !== undefined )
            {
                vm.size = 'min-width:' + vm.width + 'px;';
            }

            if( vm.height !== undefined )
            {
                vm.size += 'min-height:' + vm.height + 'px;';
            }
        }
    }
</script>