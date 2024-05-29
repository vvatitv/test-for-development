<template>
    <header class="header">
        <div class="container">
            <div class="header-main-container">
                <div class="header-logo-container">
                    <a :href="asset('/')">
                        <img :src="asset('storage/images/logo-new.svg')" alt="Проектная активация" class="img-fluid" />
                    </a>
                </div>
            </div>
        </div>
    </header>
</template>
<script>
    export default {
        data() {
            return {
            }
        },
        mounted()
        {
            var vm = this;
        },
        methods:{
        }
    }
</script>