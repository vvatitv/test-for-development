<template>
    <div class="swithers-main-container --with-scroll">
        <div class="row row-cols-3">
            <div class="col">
                <div class="swither-item" :class="{'--is-active': route().current('profile.team.*') || route().current('team.show.index') && !lodash.isEmpty(team) && collect(authUser.teams).contains('id', team.id)}">
                    <template v-if="!lodash.isEmpty(authUser)">
                        <template v-if="!lodash.isEmpty(team) && collect(authUser.teams).contains('id', team.id)">
                            <!-- <a :href="route('team.show.index', team)" class="swither-item-link">
                                <span class="text">Моя команда</span>
                            </a> -->
                            <a :href="route('profile.team.index')" class="swither-item-link">
                                <span class="text">Моя команда</span>
                            </a>
                        </template>
                        <template v-else-if="!lodash.isEmpty(authUser.teams)">
                            <a :href="route('profile.team.index')" class="swither-item-link">
                                <span class="text">Моя команда</span>
                            </a>
                        </template>
                        <template v-else-if="lodash.isEmpty(authUser)">
                            <a :href="route('profile.team.index')" class="swither-item-link">
                                <span class="text">Моя команда</span>
                            </a>
                        </template>
                        <template v-else>
                            <div class="swither-item-link --disabled">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13.5" height="15" viewBox="0 0 13.5 15"><path d="m9.15,15h-4.8c-1.38,0-2.07,0-2.71-.33-.57-.29-1.02-.74-1.31-1.31-.33-.64-.33-1.33-.33-2.71v-1.05c0-1.38,0-2.07.33-2.71.29-.57.74-1.02,1.31-1.31.19-.1.39-.17.61-.22v-.86C2.25,2.02,4.27,0,6.75,0s4.5,2.02,4.5,4.5v.86c.22.05.42.12.61.22.57.29,1.02.74,1.31,1.31.33.64.33,1.33.33,2.71v1.05c0,1.38,0,2.07-.33,2.71-.29.57-.74,1.02-1.31,1.31-.64.33-1.33.33-2.71.33ZM4.35,6.75c-1.1,0-1.71,0-2.03.16-.28.14-.51.37-.66.66-.16.32-.16.93-.16,2.03v1.05c0,1.1,0,1.71.16,2.03.15.28.37.51.66.66.32.16.93.16,2.03.16h4.8c1.1,0,1.71,0,2.03-.16.28-.14.51-.38.66-.66.16-.32.16-.93.16-2.03v-1.05c0-1.1,0-1.71-.16-2.03-.14-.28-.37-.51-.66-.66-.32-.16-.93-.16-2.03-.16h-4.8ZM6.75,1.5c-1.65,0-3,1.35-3,3v.75c.18,0,.38,0,.6,0h4.8c.22,0,.42,0,.6,0v-.75c0-1.65-1.35-3-3-3Zm0,10.12c-.41,0-.75-.34-.75-.75v-1.5c0-.41.34-.75.75-.75s.75.34.75.75v1.5c0,.41-.34.75-.75.75Z" fill="#989fa0" stroke-width="0"/></svg>
                                </span>
                                <span class="text">Моя команда</span>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="swither-item-link --disabled">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13.5" height="15" viewBox="0 0 13.5 15"><path d="m9.15,15h-4.8c-1.38,0-2.07,0-2.71-.33-.57-.29-1.02-.74-1.31-1.31-.33-.64-.33-1.33-.33-2.71v-1.05c0-1.38,0-2.07.33-2.71.29-.57.74-1.02,1.31-1.31.19-.1.39-.17.61-.22v-.86C2.25,2.02,4.27,0,6.75,0s4.5,2.02,4.5,4.5v.86c.22.05.42.12.61.22.57.29,1.02.74,1.31,1.31.33.64.33,1.33.33,2.71v1.05c0,1.38,0,2.07-.33,2.71-.29.57-.74,1.02-1.31,1.31-.64.33-1.33.33-2.71.33ZM4.35,6.75c-1.1,0-1.71,0-2.03.16-.28.14-.51.37-.66.66-.16.32-.16.93-.16,2.03v1.05c0,1.1,0,1.71.16,2.03.15.28.37.51.66.66.32.16.93.16,2.03.16h4.8c1.1,0,1.71,0,2.03-.16.28-.14.51-.38.66-.66.16-.32.16-.93.16-2.03v-1.05c0-1.1,0-1.71-.16-2.03-.14-.28-.37-.51-.66-.66-.32-.16-.93-.16-2.03-.16h-4.8ZM6.75,1.5c-1.65,0-3,1.35-3,3v.75c.18,0,.38,0,.6,0h4.8c.22,0,.42,0,.6,0v-.75c0-1.65-1.35-3-3-3Zm0,10.12c-.41,0-.75-.34-.75-.75v-1.5c0-.41.34-.75.75-.75s.75.34.75.75v1.5c0,.41-.34.75-.75.75Z" fill="#989fa0" stroke-width="0"/></svg>
                            </span>
                            <span class="text">Моя команда</span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="col">
    			<div class="swither-item" :class="{'--is-active': route().current('team.show.step.show.task.index')}">
                    <template v-if="!lodash.isEmpty(authUser)">
                        <template v-if="!lodash.isEmpty(authUser.team)">
                            <a :href="route('task.index')" class="swither-item-link">
                                <span class="text">Задания конкурса</span>
                            </a>
                            <!-- <a :href="route('team.show.step.show.task.index', {
                                team: ( !lodash.isEmpty(team) && collect(authUser.teams).contains('id', team.id) ? team : authUser.team ),
                                step: step
                            })" class="swither-item-link">
                                <span class="text">Задания конкурса</span>
                            </a> -->
                        </template>
                        <template v-else-if="lodash.isEmpty(authUser)">
                            <a :href="route('task.index')" class="swither-item-link">
                                <span class="text">Задания конкурса</span>
                            </a>
                        </template>
                        <template v-else>
                            <div class="swither-item-link --disabled">
                                <span class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13.5" height="15" viewBox="0 0 13.5 15"><path d="m9.15,15h-4.8c-1.38,0-2.07,0-2.71-.33-.57-.29-1.02-.74-1.31-1.31-.33-.64-.33-1.33-.33-2.71v-1.05c0-1.38,0-2.07.33-2.71.29-.57.74-1.02,1.31-1.31.19-.1.39-.17.61-.22v-.86C2.25,2.02,4.27,0,6.75,0s4.5,2.02,4.5,4.5v.86c.22.05.42.12.61.22.57.29,1.02.74,1.31,1.31.33.64.33,1.33.33,2.71v1.05c0,1.38,0,2.07-.33,2.71-.29.57-.74,1.02-1.31,1.31-.64.33-1.33.33-2.71.33ZM4.35,6.75c-1.1,0-1.71,0-2.03.16-.28.14-.51.37-.66.66-.16.32-.16.93-.16,2.03v1.05c0,1.1,0,1.71.16,2.03.15.28.37.51.66.66.32.16.93.16,2.03.16h4.8c1.1,0,1.71,0,2.03-.16.28-.14.51-.38.66-.66.16-.32.16-.93.16-2.03v-1.05c0-1.1,0-1.71-.16-2.03-.14-.28-.37-.51-.66-.66-.32-.16-.93-.16-2.03-.16h-4.8ZM6.75,1.5c-1.65,0-3,1.35-3,3v.75c.18,0,.38,0,.6,0h4.8c.22,0,.42,0,.6,0v-.75c0-1.65-1.35-3-3-3Zm0,10.12c-.41,0-.75-.34-.75-.75v-1.5c0-.41.34-.75.75-.75s.75.34.75.75v1.5c0,.41-.34.75-.75.75Z" fill="#989fa0" stroke-width="0"/></svg>
                                </span>
                                <span class="text">Задания конкурса</span>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <div class="swither-item-link --disabled">
                            <span class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13.5" height="15" viewBox="0 0 13.5 15"><path d="m9.15,15h-4.8c-1.38,0-2.07,0-2.71-.33-.57-.29-1.02-.74-1.31-1.31-.33-.64-.33-1.33-.33-2.71v-1.05c0-1.38,0-2.07.33-2.71.29-.57.74-1.02,1.31-1.31.19-.1.39-.17.61-.22v-.86C2.25,2.02,4.27,0,6.75,0s4.5,2.02,4.5,4.5v.86c.22.05.42.12.61.22.57.29,1.02.74,1.31,1.31.33.64.33,1.33.33,2.71v1.05c0,1.38,0,2.07-.33,2.71-.29.57-.74,1.02-1.31,1.31-.64.33-1.33.33-2.71.33ZM4.35,6.75c-1.1,0-1.71,0-2.03.16-.28.14-.51.37-.66.66-.16.32-.16.93-.16,2.03v1.05c0,1.1,0,1.71.16,2.03.15.28.37.51.66.66.32.16.93.16,2.03.16h4.8c1.1,0,1.71,0,2.03-.16.28-.14.51-.38.66-.66.16-.32.16-.93.16-2.03v-1.05c0-1.1,0-1.71-.16-2.03-.14-.28-.37-.51-.66-.66-.32-.16-.93-.16-2.03-.16h-4.8ZM6.75,1.5c-1.65,0-3,1.35-3,3v.75c.18,0,.38,0,.6,0h4.8c.22,0,.42,0,.6,0v-.75c0-1.65-1.35-3-3-3Zm0,10.12c-.41,0-.75-.34-.75-.75v-1.5c0-.41.34-.75.75-.75s.75.34.75.75v1.5c0,.41-.34.75-.75.75Z" fill="#989fa0" stroke-width="0"/></svg>
                            </span>
                            <span class="text">Задания конкурса</span>
                        </div>
                    </template>
    			</div>
            </div>
            <div class="col">
    			<div class="swither-item" :class="{'--is-active': route().current('step.show.rating.*')}">
                    <!-- <a :href="route('step.show.rating.index', current)" class="swither-item-link">
                        <span class="text">Рейтинг</span>
                    </a> -->
                    <a :href="route('rating.index')" class="swither-item-link">
                        <span class="text">Рейтинг</span>
                    </a>
    			</div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props:{
            step:{
                type: Object,
                default: null
            },
            current:{
                type: Object,
                default: null
            },
            team:{
                type: Object,
                default: null
            },
        }
    }
</script>