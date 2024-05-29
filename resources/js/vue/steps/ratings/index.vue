<template>
    <div class="container">
        <div class="main-page-container --page-ratings">
            <div class="page-body">
                <template v-if="!lodash.isEmpty(teamsData['all']) || !lodash.isEmpty(teamsData['quiz']) || !lodash.isEmpty(teamsData['passport']) || !lodash.isEmpty(teamsData['tracksIdea']) || !lodash.isEmpty(teamsData['teamtracktakesurvey']) || !lodash.isEmpty(teamsData['teamtrackselectioncasepart2']) || !lodash.isEmpty(teamsData['teamtakequest']) || !lodash.isEmpty(teamsData['teamtrackpresentation']) || !lodash.isEmpty(teamsData['awarding'])">
                    <div class="ratings-main-table-container">
                        <template>
                            <div class="heading-container">
                                <h3>Команды</h3>
                            </div>
                            <div class="swithers-main-container --with-scroll d-xl-none">
                                <div class="row row-cols-3">
                                    <div class="col">
                                        <div class="swither-item --project-experience" :class="{'--is-active': trackRatingSwitcher == 'tracksIdea'}">
                                            <button role="button" type="button" class="swither-item-button" @click="handleTrackRatingSwitcher('tracksIdea')">
                                                <span class="text">Проектный опыт</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="swither-item --project-culture" :class="{'--is-active': trackRatingSwitcher == 'teamtracktakesurvey'}">
                                            <button role="button" type="button" class="swither-item-button" @click="handleTrackRatingSwitcher('teamtracktakesurvey')">
                                                <span class="text">Проектная культура</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="swither-item --project-solution" :class="{'--is-active': trackRatingSwitcher == 'teamtrackselectioncasepart2'}">
                                            <button role="button" type="button" class="swither-item-button" @click="handleTrackRatingSwitcher('teamtrackselectioncasepart2')">
                                                <span class="text">Проектное решение</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ratings-tracks-container">
                                <div class="row row-cols-1 row-cols-sm-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-3 row-cols-xxl-3 row-cols-wxga-3 row-cols-fhd-3">
                                    <div class="col" :class="{'d-none': !$screen.xl && trackRatingSwitcher != 'tracksIdea'}">
                                        <div class="card h-100 --project-experience">
                                            <div class="card-header">
                                                <div class="labels">
                                                    <span class="icon">
                                                        <img :src="asset('storage/images/trophy.svg')" alt="кубок">
                                                    </span>
                                                    <span class="text">Победители трека</span>
                                                </div>
                                                <h3>«Проектный опыт»</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="ratings-table-container --small-table">
                                                    <template v-if="!lodash.isEmpty(teamsData['tracksIdea'])">
                                                        <template v-if="!lodash.isEmpty(teamsData['tracksIdea'].data)">
                                                            <div class="table-responsive js-notice-helper-init">
                                                                <table class="table --small-table --v2">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="--team-list-number">№</th>
                                                                            <th class="--team-info">Команда</th>
                                                                            <th class="--task-next-step text-center">
                                                                                <span class="d-inline-flex align-items-center">
                                                                                    <span class="text">Оценка</span>
                                                                                    <span class="icon ml-3 d-inline-flex align-items-center">
                                                                                        <v-popover offset="5" popoverBaseClass="tooltip popover" trigger="hover" placement="top">
                                                                                            <div class="tooltip-icon">
                                                                                                <a :href="asset('storage/docs/metodika-ocenki-proektnyj-opyt.pdf')" target="_blank">
                                                                                                    <span class="icon">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M9,18A9,9,0,1,0,0,9,9,9,0,0,0,9,18Z" fill="#F7F8FC"/><path d="M9,15a1.13,1.13,0,1,0-1.12-1.12A1.13,1.13,0,0,0,9,15Z" fill="#4C595C"/><path d="M9,11.25a.76.76,0,0,1-.75-.75V9.75A.76.76,0,0,1,9,9a1.85,1.85,0,0,0,1-.32,1.78,1.78,0,0,0,.69-.84,1.87,1.87,0,0,0-.4-2,1.91,1.91,0,0,0-1-.51,1.85,1.85,0,0,0-1.09.1,2,2,0,0,0-.84.69,1.85,1.85,0,0,0-.31,1,.75.75,0,0,1-1.5,0,3.39,3.39,0,0,1,4-3.32,3.49,3.49,0,0,1,1.73.93,3.36,3.36,0,0,1,.92,1.73,3.38,3.38,0,0,1-2.56,4v.08A.76.76,0,0,1,9,11.25Z" fill="#4C595C"/></svg>
                                                                                                    </span>
                                                                                                </a>
                                                                                            </div>
                                                                                            <template slot="popover">
                                                                                                <div class="tooltip-description text-center"><p>Нажмите, чтобы посмотреть методику оценки</p></div>
                                                                                            </template>
                                                                                        </v-popover>
                                                                                    </span>
                                                                                </span>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="(team, teamIndex) in teamsData['tracksIdea'].data" :key="teamIndex + '-' + team.id" :class="{'--is-better': teamIndex < 3 && teamsData['tracksIdea'].current_page == 1 }">
                                                                            <td class="--team-list-number">{{ teamsData['tracksIdea'].from + teamIndex }}</td>
                                                                            <td class="--team-info">
                                                                                <div class="team-info">
                                                                                    <template v-if="!lodash.isEmpty(team.badges) || ( isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id )">
                                                                                        <div class="team-badges-container">
                                                                                            <ul>
                                                                                                <template v-if="isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id">
                                                                                                    <li>
                                                                                                        <span class="icon">
                                                                                                            <img :src="asset('storage/images/icon-lightning-color.svg')" alt="" class="img-fluid">
                                                                                                        </span>
                                                                                                        <span class="text">Ваша команда</span>
                                                                                                    </li>
                                                                                                </template>
                                                                                                <template v-if="!lodash.isEmpty(team.badges)">
                                                                                                    <template v-for="(badge, badgeIndex) in team.badges">
                                                                                                        <li :key="'team-' + team.id + '-badge-' + badge.id + '-' + badgeIndex">
                                                                                                            <v-popover offset="5" popoverBaseClass="tooltip popover" :trigger="$screen.lg ? 'hover' : 'click'" placement="top">
                                                                                                                <div class="tooltip-icon">
                                                                                                                    <template v-if="badge.type.slug == 'badge-creative-potential'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-erudite'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-sturman'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-best-idea'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-top-3-project-culture'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                </div>
                                                                                                                <template slot="popover">
                                                                                                                    <div class="tooltip-description text-center" v-html="( !lodash.isEmpty(badge.description) ? badge.description : badge.type.description )"></div>
                                                                                                                </template>
                                                                                                            </v-popover>
                                                                                                        </li>
                                                                                                    </template>
                                                                                                </template>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </template>
                                                                                    <h4>{{ team.name }}</h4>
                                                                                    <template v-if="!lodash.isEmpty(team.organization)">
                                                                                        <div class="team-organization">{{ team.organization.name|StrLimit(70) }}</div>
                                                                                    </template>
                                                                                </div>
                                                                            </td>
                                                                            <td class="--task-counts-points-v2 text-right">
                                                                                <template v-if="!lodash.isEmpty(team.tracks_idea) && !lodash.isEmpty(team.tracks_idea.score)">
                                                                                    <span>{{ team.tracks_idea.score }}</span>
                                                                                </template>
                                                                                <template v-else>—</template>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <div class="table-notice-helper d-sm-none">
                                                                    <svg class="icon-swipe" xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 30 25"><path class="icon-right" d="M30,3.5,26.56,7l-.81-.82,2.06-2.1H22V2.92h5.81L25.74.82,26.55,0Z"/><path class="icon-left" d="M8,4.08H2.19l2.06,2.1L3.44,7,0,3.5,3.45,0l.81.82L2.19,2.92H8Z"/><path class="icon-hand" d="M25.41,11.18l-5.08-3A2.32,2.32,0,0,0,19,7.74a2.37,2.37,0,0,0-1.72.75v-5a2.46,2.46,0,0,0-.71-1.74A2.38,2.38,0,0,0,14.84,1h0a2.44,2.44,0,0,0-2.41,2.46V14.6l-.69-.7a4.7,4.7,0,0,0-3.36-1.42A4.75,4.75,0,0,0,6.32,13a1.26,1.26,0,0,0-.69.92A1.32,1.32,0,0,0,6,15l7.69,7.83A7.59,7.59,0,0,0,19,25a7.73,7.73,0,0,0,7.65-7.79V13.29A2.42,2.42,0,0,0,25.41,11.18Zm0,6A6.46,6.46,0,0,1,19,23.73h0a6.31,6.31,0,0,1-4.41-1.8L6.87,14.1a3.37,3.37,0,0,1,1.5-.35h0a3.42,3.42,0,0,1,2.47,1.05l1.53,1.55a.52.52,0,0,0,.36.16l.19,0a1.18,1.18,0,0,0,.75-1.1V3.46a1.17,1.17,0,0,1,1.16-1.19h0A1.18,1.18,0,0,1,16,3.46v6.91a.6.6,0,0,0,.39.56l.2,0a.58.58,0,0,0,.45-.22l1-1.3A1.16,1.16,0,0,1,19,9a1.12,1.12,0,0,1,.68.23l5.12,3a1.18,1.18,0,0,1,.59,1Z"/></svg>
                                                                </div>
                                                                <template v-if="teamsDataLoader['tracksIdea']">
                                                                    <div class="table-loader-pagination-container">
                                                                        <components-loader height="50" />
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="table-empty-container">
                                                                <h3>По вашему запросу ничего не найдено</h3>
                                                                <p>Попробуйте изменить формулировку вашего запроса</p>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <components-loader height="250"  />
                                                    </template>
                                                </div>
                                            </div>
                                            <template v-if="!lodash.isEmpty(teamsData['tracksIdea']) && !lodash.isEmpty(teamsData['tracksIdea'].data) && teamsData['tracksIdea'].last_page > 1">
                                                <div class="card-footer">
                                                    <div class="table-pagination-container">
                                                        <components-pagination
                                                            :data="teamsData['tracksIdea']"
                                                            :limit="1"
                                                            :show-disabled="true"
                                                            size="small"
                                                            @pagination-change-page="getTeams($event, 'tracksIdea', filterSeachTeamOrOrganizationValue['tracksIdea'])"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col" :class="{'d-none': !$screen.xl && trackRatingSwitcher != 'teamtracktakesurvey'}">
                                        <div class="card h-100 --project-culture">
                                            <div class="card-header">
                                                <div class="labels">
                                                    <span class="icon">
                                                        <img :src="asset('storage/images/trophy.svg')" alt="кубок">
                                                    </span>
                                                    <span class="text">Победители трека</span>
                                                </div>
                                                <h3>«Проектная культура»</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="ratings-table-container --small-table">
                                                    <template  v-if="!lodash.isEmpty(teamsData['teamtracktakesurvey'])">
                                                        <template v-if="!lodash.isEmpty(teamsData['teamtracktakesurvey'].data)">
                                                            <div class="table-responsive js-notice-helper-init">
                                                                <table class="table --small-table --v2">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="--team-list-number">№</th>
                                                                            <th class="--team-info">Команда</th>
                                                                            <th class="--task-next-step text-center">
                                                                                <span class="d-inline-flex align-items-center">
                                                                                    <span class="text">Оценка</span>
                                                                                    <span class="icon ml-3 d-inline-flex align-items-center">
                                                                                        <v-popover offset="5" popoverBaseClass="tooltip popover" trigger="hover" placement="top">
                                                                                            <div class="tooltip-icon">
                                                                                                <a :href="asset('storage/docs/metodika-ocenki-proektnaya-kultura.pdf')" target="_blank">
                                                                                                    <span class="icon">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M9,18A9,9,0,1,0,0,9,9,9,0,0,0,9,18Z" fill="#F7F8FC"/><path d="M9,15a1.13,1.13,0,1,0-1.12-1.12A1.13,1.13,0,0,0,9,15Z" fill="#4C595C"/><path d="M9,11.25a.76.76,0,0,1-.75-.75V9.75A.76.76,0,0,1,9,9a1.85,1.85,0,0,0,1-.32,1.78,1.78,0,0,0,.69-.84,1.87,1.87,0,0,0-.4-2,1.91,1.91,0,0,0-1-.51,1.85,1.85,0,0,0-1.09.1,2,2,0,0,0-.84.69,1.85,1.85,0,0,0-.31,1,.75.75,0,0,1-1.5,0,3.39,3.39,0,0,1,4-3.32,3.49,3.49,0,0,1,1.73.93,3.36,3.36,0,0,1,.92,1.73,3.38,3.38,0,0,1-2.56,4v.08A.76.76,0,0,1,9,11.25Z" fill="#4C595C"/></svg>
                                                                                                    </span>
                                                                                                </a>
                                                                                            </div>
                                                                                            <template slot="popover">
                                                                                                <div class="tooltip-description text-center"><p>Нажмите, чтобы посмотреть методику оценки</p></div>
                                                                                            </template>
                                                                                        </v-popover>
                                                                                    </span>
                                                                                </span>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="(team, teamIndex) in teamsData['teamtracktakesurvey'].data" :key="teamIndex + '-' + team.id" :class="{'--has-certification': teamIndex < 3 && collect(team.members).filter((member) => collect(member.certifications).filter((certification) => certification.type.slug == 'certificate-participant').count()).count(), '--is-better': teamIndex < 7 && teamsData['teamtracktakesurvey'].current_page == 1 }">
                                                                            <td class="--team-list-number">{{ teamsData['teamtracktakesurvey'].from + teamIndex }}</td>
                                                                            <td class="--team-info">
                                                                                <div class="team-info">
                                                                                    <template v-if="!lodash.isEmpty(team.badges) || ( isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id )">
                                                                                        <div class="team-badges-container">
                                                                                            <ul>
                                                                                                <template v-if="isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id">
                                                                                                    <li>
                                                                                                        <span class="icon">
                                                                                                            <img :src="asset('storage/images/icon-lightning-color.svg')" alt="" class="img-fluid">
                                                                                                        </span>
                                                                                                        <span class="text">Ваша команда</span>
                                                                                                    </li>
                                                                                                </template>
                                                                                                <template v-if="!lodash.isEmpty(team.badges)">
                                                                                                    <template v-for="(badge, badgeIndex) in team.badges">
                                                                                                        <li :key="'team-' + team.id + '-badge-' + badge.id + '-' + badgeIndex">
                                                                                                            <v-popover offset="5" popoverBaseClass="tooltip popover" :trigger="$screen.lg ? 'hover' : 'click'" placement="top">
                                                                                                                <div class="tooltip-icon">
                                                                                                                    <template v-if="badge.type.slug == 'badge-creative-potential'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-erudite'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-sturman'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-best-idea'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-top-3-project-culture'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                </div>
                                                                                                                <template slot="popover">
                                                                                                                    <div class="tooltip-description text-center" v-html="( !lodash.isEmpty(badge.description) ? badge.description : badge.type.description )"></div>
                                                                                                                </template>
                                                                                                            </v-popover>
                                                                                                        </li>
                                                                                                    </template>
                                                                                                </template>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </template>
                                                                                    <h4>{{ team.name }}</h4>
                                                                                    <template v-if="!lodash.isEmpty(team.organization)">
                                                                                        <div class="team-organization">{{ team.organization.name|StrLimit(70) }}</div>
                                                                                    </template>
                                                                                </div>
                                                                            </td>
                                                                            <td class="--task-counts-points-v2 text-right">
                                                                                <template v-if="!lodash.isEmpty(team.teamtracktakesurvey) && !lodash.isEmpty(team.teamtracktakesurvey.score)">
                                                                                    <span>{{ team.teamtracktakesurvey.score }}</span>
                                                                                </template>
                                                                                <template v-else>—</template>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <div class="table-notice-helper d-sm-none">
                                                                    <svg class="icon-swipe" xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 30 25"><path class="icon-right" d="M30,3.5,26.56,7l-.81-.82,2.06-2.1H22V2.92h5.81L25.74.82,26.55,0Z"/><path class="icon-left" d="M8,4.08H2.19l2.06,2.1L3.44,7,0,3.5,3.45,0l.81.82L2.19,2.92H8Z"/><path class="icon-hand" d="M25.41,11.18l-5.08-3A2.32,2.32,0,0,0,19,7.74a2.37,2.37,0,0,0-1.72.75v-5a2.46,2.46,0,0,0-.71-1.74A2.38,2.38,0,0,0,14.84,1h0a2.44,2.44,0,0,0-2.41,2.46V14.6l-.69-.7a4.7,4.7,0,0,0-3.36-1.42A4.75,4.75,0,0,0,6.32,13a1.26,1.26,0,0,0-.69.92A1.32,1.32,0,0,0,6,15l7.69,7.83A7.59,7.59,0,0,0,19,25a7.73,7.73,0,0,0,7.65-7.79V13.29A2.42,2.42,0,0,0,25.41,11.18Zm0,6A6.46,6.46,0,0,1,19,23.73h0a6.31,6.31,0,0,1-4.41-1.8L6.87,14.1a3.37,3.37,0,0,1,1.5-.35h0a3.42,3.42,0,0,1,2.47,1.05l1.53,1.55a.52.52,0,0,0,.36.16l.19,0a1.18,1.18,0,0,0,.75-1.1V3.46a1.17,1.17,0,0,1,1.16-1.19h0A1.18,1.18,0,0,1,16,3.46v6.91a.6.6,0,0,0,.39.56l.2,0a.58.58,0,0,0,.45-.22l1-1.3A1.16,1.16,0,0,1,19,9a1.12,1.12,0,0,1,.68.23l5.12,3a1.18,1.18,0,0,1,.59,1Z"/></svg>
                                                                </div>
                                                                <template v-if="teamsDataLoader['teamtracktakesurvey']">
                                                                    <div class="table-loader-pagination-container">
                                                                        <components-loader height="50" />
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="table-empty-container">
                                                                <h3>По вашему запросу ничего не найдено</h3>
                                                                <p>Попробуйте изменить формулировку вашего запроса</p>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <components-loader height="250"  />
                                                    </template>
                                                </div>
                                            </div>
                                            <template v-if="!lodash.isEmpty(teamsData['teamtracktakesurvey']) && !lodash.isEmpty(teamsData['teamtracktakesurvey'].data) && teamsData['teamtracktakesurvey'].last_page > 1">
                                                <div class="card-footer">
                                                    <div class="table-pagination-container">
                                                        <components-pagination
                                                            :data="teamsData['teamtracktakesurvey']"
                                                            :limit="1"
                                                            :show-disabled="true"
                                                            size="small"
                                                            @pagination-change-page="getTeams($event, 'teamtracktakesurvey', filterSeachTeamOrOrganizationValue['teamtracktakesurvey'])"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col" :class="{'d-none': !$screen.xl && trackRatingSwitcher != 'teamtrackselectioncasepart2'}">
                                        <div class="card h-100 --project-solution">
                                            <div class="card-header">
                                                <div class="labels">
                                                    <span class="icon">
                                                        <img :src="asset('storage/images/medal.svg')" alt="медаль">
                                                    </span>
                                                    <span class="text">Финалисты трека</span>
                                                </div>
                                                <h3>«Проектное решение»</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="ratings-table-container --small-table">
                                                    <template  v-if="!lodash.isEmpty(teamsData['teamtrackselectioncasepart2'])">
                                                        <template v-if="!lodash.isEmpty(teamsData['teamtrackselectioncasepart2'].data)">
                                                            <div class="table-responsive js-notice-helper-init">
                                                                <table class="table --small-table --v2">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="--team-list-number">№</th>
                                                                            <th class="--team-info">Команда</th>
                                                                            <th class="--task-next-step text-center">
                                                                                <span class="d-inline-flex align-items-center">
                                                                                    <span class="text">Оценка</span>
                                                                                    <span class="icon ml-3 d-inline-flex align-items-center">
                                                                                        <v-popover offset="5" popoverBaseClass="tooltip popover" trigger="hover" placement="top">
                                                                                            <div class="tooltip-icon">
                                                                                                <a :href="asset('storage/docs/metodika-ocenki-proektnoe-reshenie.pdf')" target="_blank">
                                                                                                    <span class="icon">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M9,18A9,9,0,1,0,0,9,9,9,0,0,0,9,18Z" fill="#F7F8FC"/><path d="M9,15a1.13,1.13,0,1,0-1.12-1.12A1.13,1.13,0,0,0,9,15Z" fill="#4C595C"/><path d="M9,11.25a.76.76,0,0,1-.75-.75V9.75A.76.76,0,0,1,9,9a1.85,1.85,0,0,0,1-.32,1.78,1.78,0,0,0,.69-.84,1.87,1.87,0,0,0-.4-2,1.91,1.91,0,0,0-1-.51,1.85,1.85,0,0,0-1.09.1,2,2,0,0,0-.84.69,1.85,1.85,0,0,0-.31,1,.75.75,0,0,1-1.5,0,3.39,3.39,0,0,1,4-3.32,3.49,3.49,0,0,1,1.73.93,3.36,3.36,0,0,1,.92,1.73,3.38,3.38,0,0,1-2.56,4v.08A.76.76,0,0,1,9,11.25Z" fill="#4C595C"/></svg>
                                                                                                    </span>
                                                                                                </a>
                                                                                            </div>
                                                                                            <template slot="popover">
                                                                                                <div class="tooltip-description text-center"><p>Нажмите, чтобы посмотреть методику оценки</p></div>
                                                                                            </template>
                                                                                        </v-popover>
                                                                                    </span>
                                                                                </span>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="(team, teamIndex) in teamsData['teamtrackselectioncasepart2'].data" :key="teamIndex + '-' + team.id" :class="{'--is-better': teamIndex < 3 && teamsData['teamtrackselectioncasepart2'].current_page == 1 }">
                                                                            <td class="--team-list-number">{{ teamsData['teamtrackselectioncasepart2'].from + teamIndex }}</td>
                                                                            <td class="--team-info">
                                                                                <div class="team-info">
                                                                                    <template v-if="!lodash.isEmpty(team.badges) || ( isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id )">
                                                                                        <div class="team-badges-container">
                                                                                            <ul>
                                                                                                <template v-if="isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id">
                                                                                                    <li>
                                                                                                        <span class="icon">
                                                                                                            <img :src="asset('storage/images/icon-lightning-color.svg')" alt="" class="img-fluid">
                                                                                                        </span>
                                                                                                        <span class="text">Ваша команда</span>
                                                                                                    </li>
                                                                                                </template>
                                                                                                <template v-if="!lodash.isEmpty(team.badges)">
                                                                                                    <template v-for="(badge, badgeIndex) in team.badges">
                                                                                                        <li :key="'team-' + team.id + '-badge-' + badge.id + '-' + badgeIndex">
                                                                                                            <v-popover offset="5" popoverBaseClass="tooltip popover" :trigger="$screen.lg ? 'hover' : 'click'" placement="top">
                                                                                                                <div class="tooltip-icon">
                                                                                                                    <template v-if="badge.type.slug == 'badge-creative-potential'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-erudite'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-sturman'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-best-idea'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                    <template v-else-if="badge.type.slug == 'badge-top-3-project-culture'">
                                                                                                                        <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                    </template>
                                                                                                                </div>
                                                                                                                <template slot="popover">
                                                                                                                    <div class="tooltip-description text-center" v-html="( !lodash.isEmpty(badge.description) ? badge.description : badge.type.description )"></div>
                                                                                                                </template>
                                                                                                            </v-popover>
                                                                                                        </li>
                                                                                                    </template>
                                                                                                </template>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </template>
                                                                                    <h4>{{ team.name }}</h4>
                                                                                    <template v-if="!lodash.isEmpty(team.organization)">
                                                                                        <div class="team-organization">{{ team.organization.name|StrLimit(70) }}</div>
                                                                                    </template>
                                                                                </div>
                                                                            </td>
                                                                            <td class="--task-counts-points-v2 text-right">
                                                                                <template v-if="!lodash.isEmpty(team.teamtrackselectioncasepart2) && !lodash.isEmpty(team.teamtrackselectioncasepart2.score)">
                                                                                    <span>{{ team.teamtrackselectioncasepart2.score }}</span>
                                                                                </template>
                                                                                <template v-else>—</template>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <div class="table-notice-helper d-sm-none">
                                                                    <svg class="icon-swipe" xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 30 25"><path class="icon-right" d="M30,3.5,26.56,7l-.81-.82,2.06-2.1H22V2.92h5.81L25.74.82,26.55,0Z"/><path class="icon-left" d="M8,4.08H2.19l2.06,2.1L3.44,7,0,3.5,3.45,0l.81.82L2.19,2.92H8Z"/><path class="icon-hand" d="M25.41,11.18l-5.08-3A2.32,2.32,0,0,0,19,7.74a2.37,2.37,0,0,0-1.72.75v-5a2.46,2.46,0,0,0-.71-1.74A2.38,2.38,0,0,0,14.84,1h0a2.44,2.44,0,0,0-2.41,2.46V14.6l-.69-.7a4.7,4.7,0,0,0-3.36-1.42A4.75,4.75,0,0,0,6.32,13a1.26,1.26,0,0,0-.69.92A1.32,1.32,0,0,0,6,15l7.69,7.83A7.59,7.59,0,0,0,19,25a7.73,7.73,0,0,0,7.65-7.79V13.29A2.42,2.42,0,0,0,25.41,11.18Zm0,6A6.46,6.46,0,0,1,19,23.73h0a6.31,6.31,0,0,1-4.41-1.8L6.87,14.1a3.37,3.37,0,0,1,1.5-.35h0a3.42,3.42,0,0,1,2.47,1.05l1.53,1.55a.52.52,0,0,0,.36.16l.19,0a1.18,1.18,0,0,0,.75-1.1V3.46a1.17,1.17,0,0,1,1.16-1.19h0A1.18,1.18,0,0,1,16,3.46v6.91a.6.6,0,0,0,.39.56l.2,0a.58.58,0,0,0,.45-.22l1-1.3A1.16,1.16,0,0,1,19,9a1.12,1.12,0,0,1,.68.23l5.12,3a1.18,1.18,0,0,1,.59,1Z"/></svg>
                                                                </div>
                                                                <template v-if="teamsDataLoader['teamtrackselectioncasepart2']">
                                                                    <div class="table-loader-pagination-container">
                                                                        <components-loader height="50" />
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="table-empty-container">
                                                                <h3>По вашему запросу ничего не найдено</h3>
                                                                <p>Попробуйте изменить формулировку вашего запроса</p>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <components-loader height="250"  />
                                                    </template>
                                                </div>
                                            </div>
                                            <template v-if="!lodash.isEmpty(teamsData['teamtrackselectioncasepart2']) && !lodash.isEmpty(teamsData['teamtrackselectioncasepart2'].data) && teamsData['teamtrackselectioncasepart2'].last_page > 1">
                                                <div class="card-footer">
                                                    <div class="table-pagination-container">
                                                        <components-pagination
                                                            :data="teamsData['teamtrackselectioncasepart2']"
                                                            :limit="1"
                                                            :show-disabled="true"
                                                            size="small"
                                                            @pagination-change-page="getTeams($event, 'teamtrackselectioncasepart2', filterSeachTeamOrOrganizationValue['teamtrackselectioncasepart2'])"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ratings-users-quizzes-container">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 col-wxga-12 col-fhd-12">
                                        <div class="row row-cols-1 row-cols-xl-2">
                                            <div class="col">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <span class="icon">
                                                            <img :src="asset('storage/images/icon-brain@2x.png')" class="img-fluid" alt="">
                                                        </span>
                                                        <span class="text">Викторина «Эрудит»</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="ratings-table-container --small-table">
                                                            <div class="table-filtered-container">
                                                                <div class="form-main-container">
                                                                    <div class="form-group">
                                                                        <div class="form-field">
                                                                            <autocomplete ref="searchUserOrOrganizationAutocompleteQuiz" :search="(text) => filterOrganizationOrUsersSearch('quiz', text)" :debounce-time="500" :get-result-value="filterOrganizationOrUsersSearchResultValue" @submit="(result) => filterOrganizationOrUsersSearchHandleSubmit('quiz', result)">
                                                                                <template #default="{ rootProps, inputProps, inputListeners, resultListProps, resultListListeners, results, resultProps }">
                                                                                    <div v-bind="rootProps">
                                                                                        <div class="form-field__control form-field__control_search" :class="[ { 'autocomplete-input-no-results': filterSearchOrganizationOrUsersNoResults('quiz') }, { 'autocomplete-input-focused': filterSearchUserOrOrganizationsInputFocus['quiz']  } ]">
                                                                                            <input id="filter_search_teams_and_organization_input_quiz" v-bind="inputProps" v-on="inputListeners" placeholder=" " class="form-field__search" @focus="filterSearchUserOrOrganizationsInputFocus['quiz']  = true" @blur="filterSearchUserOrOrganizationsInputFocus['quiz']  = false" />
                                                                                            <label for="filter_search_teams_and_organization_input_quiz" class="form-field__label">Поиск по ФИО и названию организации</label>
                                                                                            <template v-if="!lodash.isEmpty(inputProps.value.toString())">
                                                                                                <button class="form-field__button_search_remove_text" type="button" @click="filterSearchOrganizationOrUsersClearInput('quiz', 'searchUserOrOrganizationAutocompleteQuiz')">
                                                                                                    <span class="form-field__button_icon icon">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15"><path d="M14.02175,15a.97512.97512,0,0,1-.69166-.2866L7.50024,8.88307,1.6699,14.7134A.97814.97814,0,0,1,.2866,13.33009L6.11645,7.50024.2866,1.6699A.97814.97814,0,0,1,1.6699.2866L7.50024,6.11645,13.33009.2866A.97814.97814,0,0,1,14.7134,1.6699L8.88307,7.50024l5.83033,5.82985A.97826.97826,0,0,1,14.02175,15Z" /></svg>
                                                                                                    </span>
                                                                                                </button>
                                                                                            </template>
                                                                                            <span class="form-field__button_icon icon">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><path d="M6.2,12.41A6.21,6.21,0,1,1,12.41,6.2,6.21,6.21,0,0,1,6.2,12.41Zm0-11.14A4.94,4.94,0,1,0,11.14,6.2,4.95,4.95,0,0,0,6.2,1.27Z"/><path d="M13.36,14a.63.63,0,0,1-.45-.19L9.69,10.59a.64.64,0,0,1,.9-.9l3.22,3.22a.63.63,0,0,1,0,.9A.6.6,0,0,1,13.36,14Z"/></svg>
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="autocomplete-result-container --empty" v-if="filterSearchOrganizationOrUsersNoResults('quiz')">
                                                                                            <h3>По вашему запросу ничего не найдено</h3>
                                                                                            <p>Попробуйте изменить формулировку вашего запроса</p>
                                                                                        </div>
                                                                                        <div class="autocomplete-result-container" v-bind="resultListProps" v-on="resultListListeners">
                                                                                            <ul class="autocomplete-result-list">
                                                                                                <li class="autocomplete-result-list-item" v-for="(item, index) in results" :key="resultProps[index].id" v-bind="resultProps[index]">
                                                                                                    <template v-if="item.type == 'organization'">
                                                                                                        <span>{{ item.data.name }}</span>
                                                                                                    </template>
                                                                                                    <template v-if="item.type == 'user'">
                                                                                                        <span>{{ item.data.full_name }}</span>
                                                                                                    </template>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </template>
                                                                            </autocomplete>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <template v-if="!lodash.isEmpty(usersData['quiz'])">
                                                                <template v-if="!lodash.isEmpty(usersData['quiz'].data)">
                                                                    <div class="table-responsive js-notice-helper-init">
                                                                        <table class="table --small-table --v2">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="--team-list-number">№</th>
                                                                                    <th class="--team-info">Участник</th>
                                                                                    <th class="--task-next-step">Баллы</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <template v-for="(member, memberIndex) in usersData['quiz'].data">
                                                                                    <tr :key="memberIndex + '-' + member.id">
                                                                                        <td class="--team-list-number">{{ usersData['quiz'].from + memberIndex }}</td>
                                                                                        <td class="--team-member">
                                                                                            <span class="--team-list-number">Место в рейтинге: <span>{{ usersData['quiz'].from + memberIndex }}</span></span>
                                                                                            <span class="member--info">
                                                                                                <span class="icon">
                                                                                                    <template v-if="!lodash.isEmpty(member.pictures.userpicture['56x56'])">
                                                                                                        <img :src="member.pictures.userpicture['56x56'].retina" alt="" class="img-fluid" />
                                                                                                    </template>
                                                                                                    <template v-else>
                                                                                                        <img :src="asset('storage/images/icon-user-avatar.svg')" alt="" class="img-fluid" />
                                                                                                    </template>
                                                                                                </span>
                                                                                                <span class="text">
                                                                                                    <h4>{{ member.full_name }}</h4>
                                                                                                    <template v-if="!lodash.isEmpty(member.organization)">
                                                                                                        <div class="member-organization">{{ member.organization.name|StrLimit(70) }}</div>
                                                                                                    </template>
                                                                                                </span>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="--task-counts-points-v2 text-right">
                                                                                            <template v-if="collect(member.quizzes).where('status_id', 200).filter((quiz) => collect(['team-take-quiz']).contains(quiz.quizze.hash)).count()">
                                                                                                <span>{{ getMemberQuizPoint(member, 'team-take-quiz') }}</span>/{{ collect(collect(member.quizzes).where('status_id', 200).filter((quiz) => collect(['team-take-quiz']).contains(quiz.quizze.hash)).first().questions).count() }}
                                                                                            </template>
                                                                                            <template v-else>
                                                                                                <span>—</span>
                                                                                            </template>
                                                                                        </td>
                                                                                    </tr>
                                                                                </template>
                                                                            </tbody>
                                                                        </table>
                                                                        <div class="table-notice-helper d-none">
                                                                            <svg class="icon-swipe" xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 30 25"><path class="icon-right" d="M30,3.5,26.56,7l-.81-.82,2.06-2.1H22V2.92h5.81L25.74.82,26.55,0Z"/><path class="icon-left" d="M8,4.08H2.19l2.06,2.1L3.44,7,0,3.5,3.45,0l.81.82L2.19,2.92H8Z"/><path class="icon-hand" d="M25.41,11.18l-5.08-3A2.32,2.32,0,0,0,19,7.74a2.37,2.37,0,0,0-1.72.75v-5a2.46,2.46,0,0,0-.71-1.74A2.38,2.38,0,0,0,14.84,1h0a2.44,2.44,0,0,0-2.41,2.46V14.6l-.69-.7a4.7,4.7,0,0,0-3.36-1.42A4.75,4.75,0,0,0,6.32,13a1.26,1.26,0,0,0-.69.92A1.32,1.32,0,0,0,6,15l7.69,7.83A7.59,7.59,0,0,0,19,25a7.73,7.73,0,0,0,7.65-7.79V13.29A2.42,2.42,0,0,0,25.41,11.18Zm0,6A6.46,6.46,0,0,1,19,23.73h0a6.31,6.31,0,0,1-4.41-1.8L6.87,14.1a3.37,3.37,0,0,1,1.5-.35h0a3.42,3.42,0,0,1,2.47,1.05l1.53,1.55a.52.52,0,0,0,.36.16l.19,0a1.18,1.18,0,0,0,.75-1.1V3.46a1.17,1.17,0,0,1,1.16-1.19h0A1.18,1.18,0,0,1,16,3.46v6.91a.6.6,0,0,0,.39.56l.2,0a.58.58,0,0,0,.45-.22l1-1.3A1.16,1.16,0,0,1,19,9a1.12,1.12,0,0,1,.68.23l5.12,3a1.18,1.18,0,0,1,.59,1Z"/></svg>
                                                                        </div>
                                                                        <template v-if="usersDataLoader['quiz']">
                                                                            <div class="table-loader-pagination-container">
                                                                                <components-loader height="50" />
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="table-empty-container">
                                                                        <h3>По вашему запросу ничего не найдено</h3>
                                                                        <p>Попробуйте изменить формулировку вашего запроса</p>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <template v-else>
                                                                <components-loader height="250"  />
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <template v-if="!lodash.isEmpty(usersData['quiz']) && !lodash.isEmpty(usersData['quiz'].data) && usersData['quiz'].last_page > 1">
                                                        <div class="card-footer">
                                                            <div class="table-pagination-container">
                                                                <components-pagination
                                                                    :data="usersData['quiz']"
                                                                    :limit="1"
                                                                    :show-disabled="true"
                                                                    size="small"
                                                                    @pagination-change-page="getUsers($event, 'quiz', filterSeachUserOrOrganizationValue['quiz'])"
                                                                />
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="card h-100">
                                                    <div class="card-header">
                                                        <span class="icon">
                                                            <img :src="asset('/storage/images/emoji/icon-ship-helm@2x.png')" class="img-fluid" alt="">
                                                        </span>
                                                        <span class="text">Викторина «Штурман»</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="ratings-table-container --small-table">
                                                            <div class="table-filtered-container">
                                                                <div class="form-main-container">
                                                                    <div class="form-group">
                                                                        <div class="form-field">
                                                                            <autocomplete ref="searchTeamOrOrganizationAutocompleteAll" :search="(text) => filterOrganizationOrTeamsSearchAll('teamtakequest', text)" :debounce-time="500" :get-result-value="filterOrganizationOrTeamsSearchResultValue" @submit="(result) => filterOrganizationOrTeamsSearchAllHandleSubmit('teamtakequest', result)">
                                                                                <template #default="{ rootProps, inputProps, inputListeners, resultListProps, resultListListeners, results, resultProps }">
                                                                                    <div v-bind="rootProps">
                                                                                        <div class="form-field__control form-field__control_search" :class="[ { 'autocomplete-input-no-results': filterSearchOrganizationOrTeamsNoResults('teamtakequest') }, { 'autocomplete-input-focused': filterSearchTeamOrOrganizationsInputFocus['teamtakequest'] } ]">
                                                                                            <input id="filter_search_teams_and_organization_input_all" v-bind="inputProps" v-on="inputListeners" placeholder=" " class="form-field__search" @focus="filterSearchTeamOrOrganizationsInputFocus['teamtakequest'] = true" @blur="filterSearchTeamOrOrganizationsInputFocus['teamtakequest'] = false" />
                                                                                            <label for="filter_search_teams_and_organization_input_all" class="form-field__label">Поиск по названию команды и организации</label>
                                                                                            <button class="form-field__button_search_remove_text" type="button" @click="filterSearchOrganizationOrTeamsClearInput('teamtakequest', 'searchTeamOrOrganizationAutocompleteAll')" v-if="!lodash.isEmpty(inputProps.value.toString())">
                                                                                                <span class="form-field__button_icon icon">
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15"><path d="M14.02175,15a.97512.97512,0,0,1-.69166-.2866L7.50024,8.88307,1.6699,14.7134A.97814.97814,0,0,1,.2866,13.33009L6.11645,7.50024.2866,1.6699A.97814.97814,0,0,1,1.6699.2866L7.50024,6.11645,13.33009.2866A.97814.97814,0,0,1,14.7134,1.6699L8.88307,7.50024l5.83033,5.82985A.97826.97826,0,0,1,14.02175,15Z" /></svg>
                                                                                                </span>
                                                                                            </button>
                                                                                            <span class="form-field__button_icon icon">
                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><path d="M6.2,12.41A6.21,6.21,0,1,1,12.41,6.2,6.21,6.21,0,0,1,6.2,12.41Zm0-11.14A4.94,4.94,0,1,0,11.14,6.2,4.95,4.95,0,0,0,6.2,1.27Z"/><path d="M13.36,14a.63.63,0,0,1-.45-.19L9.69,10.59a.64.64,0,0,1,.9-.9l3.22,3.22a.63.63,0,0,1,0,.9A.6.6,0,0,1,13.36,14Z"/></svg>
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="autocomplete-result-container --empty" v-if="filterSearchOrganizationOrTeamsNoResults('teamtakequest')">
                                                                                            <h3>По вашему запросу ничего не найдено</h3>
                                                                                            <p>Попробуйте изменить формулировку вашего запроса</p>
                                                                                        </div>
                                                                                        <div class="autocomplete-result-container" v-bind="resultListProps" v-on="resultListListeners">
                                                                                            <ul class="autocomplete-result-list">
                                                                                                <li class="autocomplete-result-list-item" v-for="(item, index) in results" :key="resultProps[index].id" v-bind="resultProps[index]">
                                                                                                    <template v-if="item.type == 'organization'">
                                                                                                        <span>{{ item.data.name }}</span>
                                                                                                    </template>
                                                                                                    <template v-if="item.type == 'team'">
                                                                                                        <span>{{ item.data.name }}</span>
                                                                                                    </template>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </template>
                                                                            </autocomplete>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <template v-if="!lodash.isEmpty(teamsData['teamtakequest'])">
                                                                <template v-if="!lodash.isEmpty(teamsData['teamtakequest'].data)">
                                                                    <div class="table-responsive js-notice-helper-init">
                                                                        <table class="table --small-table --v2 --table-team-take-quest">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="--team-list-number">№</th>
                                                                                    <th class="--team-info">Участник</th>
                                                                                    <th class="--task-next-step">Баллы</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <template v-for="(team, teamIndex) in teamsData['teamtakequest'].data">
                                                                                    <tr :key="teamIndex + '-' + team.id">
                                                                                        <td class="--team-list-number">{{ teamsData['teamtakequest'].from + teamIndex }}</td>
                                                                                        <td class="--team-info">
                                                                                            <span class="--team-list-number">Место в рейтинге: <span>{{ teamsData['teamtakequest'].from + teamIndex }}</span></span>
                                                                                            <div class="team-info">
                                                                                                <template v-if="!lodash.isEmpty(team.badges) || ( isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id )">
                                                                                                    <div class="team-badges-container">
                                                                                                        <ul>
                                                                                                            <template v-if="isAuthenticated && !lodash.isEmpty(authUser.team) && authUser.team.id == team.id">
                                                                                                                <li>
                                                                                                                    <span class="icon">
                                                                                                                        <img :src="asset('storage/images/icon-lightning-color.svg')" alt="" class="img-fluid">
                                                                                                                    </span>
                                                                                                                    <span class="text">Ваша команда</span>
                                                                                                                </li>
                                                                                                            </template>
                                                                                                            <template v-if="!lodash.isEmpty(team.badges)">
                                                                                                                <template v-for="(badge, badgeIndex) in team.badges">
                                                                                                                    <li :key="'team-' + team.id + '-badge-' + badge.id + '-' + badgeIndex">
                                                                                                                        <v-popover offset="5" popoverBaseClass="tooltip popover" :trigger="$screen.lg ? 'hover' : 'click'" placement="top">
                                                                                                                            <div class="tooltip-icon">
                                                                                                                                <template v-if="badge.type.slug == 'badge-creative-potential'">
                                                                                                                                    <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                                </template>
                                                                                                                                <template v-else-if="badge.type.slug == 'badge-erudite'">
                                                                                                                                    <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                                </template>
                                                                                                                                <template v-else-if="badge.type.slug == 'badge-sturman'">
                                                                                                                                    <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                                </template>
                                                                                                                                <template v-else-if="badge.type.slug == 'badge-best-idea'">
                                                                                                                                    <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                                </template>
                                                                                                                                <template v-else-if="badge.type.slug == 'badge-top-3-project-culture'">
                                                                                                                                    <img :src="asset('storage/images/badges/' + badge.type.slug + '.svg')" alt="" class="img-fluid">
                                                                                                                                </template>
                                                                                                                            </div>
                                                                                                                            <template slot="popover">
                                                                                                                                <div class="tooltip-description text-center" v-html="( !lodash.isEmpty(badge.description) ? badge.description : badge.type.description )"></div>
                                                                                                                            </template>
                                                                                                                        </v-popover>
                                                                                                                    </li>
                                                                                                                </template>
                                                                                                            </template>
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                </template>
                                                                                                <h4>{{ team.name }}</h4>
                                                                                                <template v-if="!lodash.isEmpty(team.organization)">
                                                                                                    <div class="team-organization">{{ team.organization.name|StrLimit(70) }}</div>
                                                                                                </template>
                                                                                            </div>
                                                                                            <span class="--task-counts-points-v2">
                                                                                                <template v-if="!lodash.isEmpty(team.teamtakequest) && !lodash.isEmpty(team.teamtakequest.score)">
                                                                                                    Баллы: <span><span>{{ parseFloat(team.teamtakequest.score).toFixed(0) }}</span>/100</span>
                                                                                                </template>
                                                                                                <template v-else>Баллы: —</template>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td class="--task-counts-points-v2 text-right">
                                                                                            <template v-if="!lodash.isEmpty(team.teamtakequest) && !lodash.isEmpty(team.teamtakequest.score)">
                                                                                                <span>{{ parseFloat(team.teamtakequest.score).toFixed(0) }}</span>/100
                                                                                            </template>
                                                                                            <template v-else>—</template>
                                                                                        </td>
                                                                                    </tr>
                                                                                </template>
                                                                            </tbody>
                                                                        </table>
                                                                        <div class="table-notice-helper d-none">
                                                                            <svg class="icon-swipe" xmlns="http://www.w3.org/2000/svg" width="30" height="25" viewBox="0 0 30 25"><path class="icon-right" d="M30,3.5,26.56,7l-.81-.82,2.06-2.1H22V2.92h5.81L25.74.82,26.55,0Z"/><path class="icon-left" d="M8,4.08H2.19l2.06,2.1L3.44,7,0,3.5,3.45,0l.81.82L2.19,2.92H8Z"/><path class="icon-hand" d="M25.41,11.18l-5.08-3A2.32,2.32,0,0,0,19,7.74a2.37,2.37,0,0,0-1.72.75v-5a2.46,2.46,0,0,0-.71-1.74A2.38,2.38,0,0,0,14.84,1h0a2.44,2.44,0,0,0-2.41,2.46V14.6l-.69-.7a4.7,4.7,0,0,0-3.36-1.42A4.75,4.75,0,0,0,6.32,13a1.26,1.26,0,0,0-.69.92A1.32,1.32,0,0,0,6,15l7.69,7.83A7.59,7.59,0,0,0,19,25a7.73,7.73,0,0,0,7.65-7.79V13.29A2.42,2.42,0,0,0,25.41,11.18Zm0,6A6.46,6.46,0,0,1,19,23.73h0a6.31,6.31,0,0,1-4.41-1.8L6.87,14.1a3.37,3.37,0,0,1,1.5-.35h0a3.42,3.42,0,0,1,2.47,1.05l1.53,1.55a.52.52,0,0,0,.36.16l.19,0a1.18,1.18,0,0,0,.75-1.1V3.46a1.17,1.17,0,0,1,1.16-1.19h0A1.18,1.18,0,0,1,16,3.46v6.91a.6.6,0,0,0,.39.56l.2,0a.58.58,0,0,0,.45-.22l1-1.3A1.16,1.16,0,0,1,19,9a1.12,1.12,0,0,1,.68.23l5.12,3a1.18,1.18,0,0,1,.59,1Z"/></svg>
                                                                        </div>
                                                                        <template v-if="teamsDataLoader['teamtakequest']">
                                                                            <div class="table-loader-pagination-container">
                                                                                <components-loader height="50" />
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template v-else>
                                                                    <div class="table-empty-container">
                                                                        <h3>По вашему запросу ничего не найдено</h3>
                                                                        <p>Попробуйте изменить формулировку вашего запроса</p>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <template v-else>
                                                                <components-loader height="250"  />
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <template v-if="!lodash.isEmpty(teamsData['teamtakequest']) && !lodash.isEmpty(teamsData['teamtakequest'].data) && teamsData['teamtakequest'].last_page > 1">
                                                        <div class="card-footer">
                                                            <div class="table-pagination-container">
                                                                <components-pagination
                                                                    :data="teamsData['teamtakequest']"
                                                                    :limit="1"
                                                                    :show-disabled="true"
                                                                    size="small"
                                                                    @pagination-change-page="getTeams($event, 'teamtakequest', filterSeachTeamOrOrganizationValue['teamtakequest'])"
                                                                />
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template v-else>
                    <components-loader height="250" />
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props:{
            step: {
                type: Object
            },
            page: {
                type: Number,
                default: 1
            }
        },
        data()
        {
            return {
                stepsLists: [],
                currentStep: [],
                pageStep: [],
                nextStep: [],
                teamsData: {
                    all: [],
                    quiz: [],
                    passport: [],
                    tracksIdea: [],
                    teamtracktakesurvey: [],
                    teamtrackselectioncasepart2: [],
                    teamtakequest: [],
                    teamtrackpresentation: [],
                    awarding: [],
                },
                usersData: {
                    all: [],
                    quiz: [],
                },
                countTeamsAllTasksArray: [],
                organizations: [],
                teams: [],
                users: [],
                filterSearchTeamOrOrganizationsInputFocus: {
                    all: false,
                    quiz: false,
                    passport: false,
                    tracksIdea: false,
                    teamtracktakesurvey: false,
                    teamtrackselectioncasepart2: false,
                    teamtakequest: false,
                    teamtrackpresentation: false,
                    awarding: false,
                },
                filterSeachTeamOrOrganizationValue: {
                    all: '',
                    quiz: '',
                    passport: '',
                    tracksIdea: '',
                    teamtracktakesurvey: '',
                    teamtrackselectioncasepart2: '',
                    teamtakequest: '',
                    teamtrackpresentation: '',
                    awarding: '',
                },
                filterSeachTeamOrOrganizationResults: {
                    all: [],
                    quiz: [],
                    passport: [],
                    tracksIdea: [],
                    teamtracktakesurvey: [],
                    teamtrackselectioncasepart2: [],
                    teamtakequest: [],
                    teamtrackpresentation: [],
                    awarding: [],
                },
                filterSearchUserOrOrganizationsInputFocus: {
                    all: false,
                    quiz: false,
                },
                filterSeachUserOrOrganizationValue: {
                    all: '',
                    quiz: '',
                },
                filterSeachUserOrOrganizationResults: {
                    all: [],
                    quiz: [],
                },
                teamsDataLoader: {
                    all: false,
                    quiz: false,
                    passport: false,
                    tracksIdea: false,
                    teamtracktakesurvey: false,
                    teamtrackselectioncasepart2: false,
                    teamtakequest: false,
                    teamtrackpresentation: false,
                    awarding: false,
                },
                usersDataLoader: {
                    all: false,
                    quiz: false,
                },
                tracks: [],
                trackRatingSwitcher: 'tracksIdea',
            }
        },
        async mounted()
        {
            var vm = this;

            await vm.getTracks();
            await vm.getPageStep(vm.step.id);
            await vm.getNextStep(vm.step.id + 1);
        },
        updated()
        {
            var vm = this;

            vm.$nextTick(() => {

                if( !vm.lodash.isEmpty(document.querySelectorAll('.js-notice-helper-init')) )
                {
                    document.querySelectorAll('.js-notice-helper-init').forEach((element) => {
                        element.classList.remove('js-notice-helper-init');
                        element.setAttribute('data-scroll', element.scrollLeft || 0);
                        element.addEventListener('scroll', vm.HelpersTableElementsEvent);
                    });
                }

            });
        },
        computed: {},
        watch:{
            'filterSeachTeamOrOrganizationValue.all': {
                handler: function (newValue, oldValue) {
                    var vm = this;
                    vm.getTeams(1, 'all', newValue);
                },
                deep: true
            },
        },
        methods:{
            getMemberQuizPoint(member, hash)
            {
                var vm = this,
                    quiz = collect(member.quizzes).where('status_id', 200).filter((quiz) => collect([hash]).contains(quiz.quizze.hash)).first(),
                    summ = 0;

                if( vm.lodash.isEmpty(quiz) )
                {
                    return summ;
                }

                if( vm.lodash.isEmpty(quiz.answers) )
                {
                    return summ;
                }

                collect(quiz.answers)
                .each((answer) => {

                    summ += answer.pivot.point;

                });

                return summ;
            },
            handleTrackRatingSwitcher(type)
            {
                var vm = this;

                if( vm.lodash.isEmpty(type) )
                {
                    vm.trackRatingSwitcher = 'tracksIdea';
                }

                vm.trackRatingSwitcher = type;
            },
            filterSearchOrganizationOrUsersNoResults(type)
            {
                var vm = this;
                return vm.filterSeachUserOrOrganizationValue[type] && vm.filterSeachUserOrOrganizationResults[type].length === 0;
            },
            filterSearchOrganizationOrUsersClearInput(type, ref)
            {
                var vm = this;

                if( !vm.lodash.isEmpty(ref) )
                {
                    vm.$refs[ref].value = '';
                    vm.filterSeachUserOrOrganizationValue[type] = '';
                    vm.getUsers(1, type);
                }
            },
            filterOrganizationOrUsersSearchResultValue(result)
            {
                var vm = this;

                switch (result.type)
                {
                    case 'user':
                        return !vm.lodash.isEmpty(result) && !vm.lodash.isEmpty(result.data) ? result.data.full_name : result;
                    break;
                    default:
                        return !vm.lodash.isEmpty(result) && !vm.lodash.isEmpty(result.data) ? result.data.name : result;
                    break;
                }
            },
            filterOrganizationOrUsersSearchHandleSubmit(type, result)
            {
                var vm = this,
                    searchText = result;

                if( !vm.lodash.isEmpty(result) )
                {
                    searchText = result;
                }
                else
                {
                    searchText = vm.filterSeachUserOrOrganizationValue[type];
                }

                vm.getUsers(1, type, searchText);
            },
            filterOrganizationOrUsersSearch(type, searchText)
            {
                var vm = this,
                    users = JSON.parse(JSON.stringify(vm.users)),
                    organizations = JSON.parse(JSON.stringify(vm.organizations));

                vm.filterSeachUserOrOrganizationValue[type] = searchText;
                
                if( searchText.length == 0 )
                {
                    vm.filterSeachUserOrOrganizationResults[type] = [];
                    return vm.filterSeachUserOrOrganizationResults[type];
                }

                vm.filterSeachUserOrOrganizationResults[type] = [];

                if( !vm.lodash.isEmpty(users) )
                {
                    users = users.filter(user => {
                        return user.full_name.toLowerCase().includes(searchText.toLowerCase())
                    });

                    if( !vm.lodash.isEmpty(users) )
                    {
                        users.forEach(user => {

                            vm.filterSeachUserOrOrganizationResults[type].push({
                                type: 'user',
                                name: user.full_name,
                                data: user
                            });

                        });
                    }
                }

                if( !vm.lodash.isEmpty(organizations) )
                {
                    organizations = organizations.filter(organization => {
                        return organization.name.toLowerCase().includes(searchText.toLowerCase())
                    });

                    if( !vm.lodash.isEmpty(organizations) )
                    {
                        organizations.forEach(organization => {

                            vm.filterSeachUserOrOrganizationResults[type].push({
                                type: 'organization',
                                name: organization.name,
                                data: organization
                            });

                        });
                    }
                }

                return vm.filterSeachUserOrOrganizationResults[type];
            },
            filterSearchOrganizationOrTeamsNoResults(type)
            {
                var vm = this;
                return vm.filterSeachTeamOrOrganizationValue[type] && vm.filterSeachTeamOrOrganizationResults[type].length === 0;
            },
            filterSearchOrganizationOrTeamsClearInput(type, ref)
            {
                var vm = this;

                if( !vm.lodash.isEmpty(ref) )
                {
                    vm.$refs[ref].value = '';
                    vm.filterSeachTeamOrOrganizationValue[type] = '';
                    vm.getTeams(1, type);
                }
            },
            filterOrganizationOrTeamsSearchResultValue(result)
            {
                var vm = this;

                return !vm.lodash.isEmpty(result) && !vm.lodash.isEmpty(result.data) ? result.data.name : result;
            },
            filterOrganizationOrTeamsSearchAllHandleSubmit(type, result)
            {
                var vm = this,
                    searchText = result;

                if( !vm.lodash.isEmpty(result) )
                {
                    searchText = result;
                }
                else
                {
                    searchText = vm.filterSeachTeamOrOrganizationValue[type];
                }

                vm.getTeams(1, type, searchText);
            },
            filterOrganizationOrTeamsSearchAll(type, searchText)
            {
                var vm = this,
                    teams = JSON.parse(JSON.stringify(vm.teams)),
                    organizations = JSON.parse(JSON.stringify(vm.organizations));

                vm.filterSeachTeamOrOrganizationValue[type] = searchText;
                
                if( searchText.length == 0 )
                {
                    vm.filterSeachTeamOrOrganizationResults[type] = [];
                    return vm.filterSeachTeamOrOrganizationResults[type];
                }

                vm.filterSeachTeamOrOrganizationResults[type] = [];

                if( !vm.lodash.isEmpty(teams) )
                {
                    teams = teams.filter(team => {
                        return team.name.toLowerCase().includes(searchText.toLowerCase())
                    });
                    if( !vm.lodash.isEmpty(teams) )
                    {
                        teams.forEach(team => {
                            vm.filterSeachTeamOrOrganizationResults[type].push({
                                type: 'team',
                                name: team.name,
                                data: team
                            });
                        });
                    }
                }

                if( !vm.lodash.isEmpty(organizations) )
                {
                    organizations = organizations.filter(organization => {
                        return organization.name.toLowerCase().includes(searchText.toLowerCase())
                    });
                    if( !vm.lodash.isEmpty(organizations) )
                    {
                        organizations.forEach(organization => {
                            vm.filterSeachTeamOrOrganizationResults[type].push({
                                type: 'organization',
                                name: organization.name,
                                data: organization
                            });
                        });
                    }
                }

                return vm.filterSeachTeamOrOrganizationResults[type];
            },
            async getTracks()
            {
                var vm = this;

                await axios
                    .get(route('api.track.index'))
                    .then((response) => {
                        vm.tracks = response.data.data;
                    })
                    .catch((error) => {
                        console.log('error', error);
                    });
            },
            async getAllOrganizations()
            {
                var vm = this;

                    await axios
                            .get(route('api.organization.index'))
                            .then((response) => {
                                vm.organizations = response.data.data;
                            })
                            .catch((error) => {
                                console.log('error', error);
                            });
            },
            async getAllTeams()
            {
                var vm = this;

                    await axios
                            .get(route('api.teams.index'), {
                                params: {
                                    with:[
                                        'tasks',
                                        'tracks',
                                    ],
                                    withCount: [
                                        'members'
                                    ],
                                    filter:{
                                        step: vm.step.id
                                    },
                                }
                            })
                            .then((response) => {

                                vm.teams = response.data.data;

                                vm.$nextTick(() => {
                                    vm.countTeamWithAllTasks();
                                });

                            })
                            .catch((error) => {
                                return resolve([]);
                            });
            },
            async getAllUsers()
            {
                var vm = this;

                    await axios
                            .get(route('api.users.index'), {
                                params: {
                                    with:[
                                        'fields',
                                        'organization',
                                    ],
                                    append: [
                                        'full_name'
                                    ],
                                    filter:{
                                        step: vm.step.id
                                    },
                                }
                            })
                            .then((response) => {

                                vm.users = response.data.data;

                            })
                            .catch((error) => {
                                return resolve([]);
                            });
            },
            getTeams(page = 1, type = 'all', searchResult = '')
            {
                var vm = this,
                    params = {
                        with:[
                            'organization',
                            'tasks',
                            'steps',
                            'tracks',
                        ],
                        withCount: [
                            'members'
                        ],
                        filter:{
                            step: vm.step.id
                        }
                    };

                switch (type)
                {
                    case 'tracksIdea':

                        params.with = [
                            'organization',
                            'tracks_idea',
                        ];

                        params.sortBy = [
                            {
                                field: 'tracks-idea-score',
                                type: 'desc'
                            }
                        ];

                        params.filter = {
                            step: vm.step.id,
                            track: 'project-experience',
                        };
                        
                    break;
                    case 'teamtracktakesurvey':
                        
                        params.with = [
                            'organization',
                            'teamtracktakesurvey',
                        ];

                        params.sortBy = [
                            {
                                field: 'team-track-take-survey-score',
                                type: 'desc'
                            }
                        ];

                        params.filter = {
                            step: vm.step.id,
                            track: 'project-culture',
                        };

                    break;
                    case 'teamtrackpresentation':
                        
                        params.with = [
                            'organization',
                            'teamtrackpresentation',
                        ];

                        params.sortBy = [
                            {
                                field: 'team-track-presentation',
                                type: 'desc'
                            }
                        ];

                        params.filter = {
                            step: vm.step.id,
                            track: 'project-culture',
                        };

                    break;
                    case 'teamtrackselectioncasepart2':

                        params.with = [
                            'organization',
                            'teamtrackselectioncasepart2',
                        ];

                        params.sortBy = [
                            {
                                field: 'team-track-selection-case-part-2-score',
                                type: 'desc'
                            }
                        ];

                        params.filter = {
                            step: vm.step.id,
                            track: 'project-solution', 
                        };

                    break;
                    case 'teamtakequest':

                        params.with = [
                            'organization',
                            'teamtakequest',
                        ];

                        params.sortBy = [
                            {
                                field: 'team-take-quest-score',
                                type: 'desc'
                            }
                        ];

                        params.filter = {
                            step: vm.step.id,
                        };

                    break;
                    case 'all':
                    default:

                        params.sortBy = [
                            {
                                field: 'tasks-counts',
                                type: 'desc'
                            },
                        ];

                    break;
                }

                if( !vm.lodash.isEmpty(searchResult) )
                {
                    if( vm.lodash.isString(searchResult) )
                    {
                        params.filter.name = {
                            text: searchResult
                        };

                        params.filter.organization = {
                            text: searchResult,
                            or: true
                        };
                    }
                    else
                    {
                        switch (searchResult.type)
                        {
                            case 'organization':
                                
                                params.filter.organization_id = searchResult.data.id;

                            break;
                            case 'team':

                                params.filter.id = searchResult.data.id;

                            break;
                            default:
                            break;
                        }
                    }
                }

                switch (vm.step.id)
                {
                    default:

                        params.pagination = {
                            perPage: 10,
                            pageName: 'page',
                            page: page
                        };

                    break;
                }

                vm.teamsDataLoader[type] = true;

                axios
                    .get(route('api.teams.index'), {
                        params: params
                    })
                    .then((response) => {

                        switch (type)
                        {
                            case 'tracksIdea':
                                vm.teamsData['tracksIdea'] = response.data.data;
                            break;
                            case 'teamtracktakesurvey':
                                vm.teamsData['teamtracktakesurvey'] = response.data.data;
                            break;
                            case 'teamtrackpresentation':
                                vm.teamsData['teamtrackpresentation'] = response.data.data;
                            break;
                            case 'teamtrackselectioncasepart2':
                                vm.teamsData['teamtrackselectioncasepart2'] = response.data.data;
                            break;
                            case 'teamtakequest':
                                vm.teamsData['teamtakequest'] = response.data.data;
                            break;
                            case 'all':
                            default:

                                switch (vm.step.id)
                                {
                                    default:

                                        vm.teamsData['all'] = response.data.data;
                                        
                                    break;
                                }

                                window.history.pushState({
                                    page: page
                                }, document.querySelector('title').text, route('step.show.rating.index', vm.step) + ( page > 1 ? '?page=' + page : '' ));

                            break;
                        }

                        vm.teamsDataLoader[type] = false;

                    })
                    .catch((error) => {
                        console.log('error', error);
                    });
            },
            getUsers(page = 1, type = 'all', searchResult = '')
            {
                var vm = this,
                    params = {
                        with:[
                            'fields',
                            'organization',
                        ],
                        append:[
                            'full_name',
                        ],
                        filter:{
                            step: vm.step.id
                        }
                    };

                switch (type)
                {
                    case 'quiz':
                        
                        params.with.push('quizzes');
                        params.with.push('quizzes.quizze');
                        params.with.push('quizzes.answers');
                        params.with.push('quizzes.questions');

                        params.sortBy = [
                            {
                                field: 'quizzes-score-w-number-passes',
                                type: 'desc',
                                params: {
                                    hash: 'team-take-quiz',
                                }
                            }
                        ];
                        
                    break;
                    default:
                    break;
                }

                if( !vm.lodash.isEmpty(searchResult) )
                {
                    switch (searchResult.type)
                    {
                        case 'organization':
                            
                            params.filter.organization_id = {
                                text: searchResult.data.id
                            };

                        break;
                        case 'user':

                            params.filter.id = {
                                text: searchResult.data.id
                            };

                        break;
                        default:
                        break;
                    }
                }

                switch (vm.step.id)
                {
                    default:

                        params.pagination = {
                            perPage: 10,
                            pageName: 'page',
                            page: page
                        };

                    break;
                }

                vm.usersDataLoader[type] = true;

                axios
                    .get(route('api.users.index'), {
                        params: params
                    })
                    .then((response) => {

                        switch (type)
                        {
                            case 'quiz':
                                vm.usersData['quiz'] = response.data.data;
                            break;
                            case 'all':
                            default:

                                window.history.pushState({
                                    page: page
                                }, document.querySelector('title').text, route('step.show.rating.index', vm.step) + ( page > 1 ? '?page=' + page : '' ));

                            break;
                        }

                        vm.usersDataLoader[type] = false;

                    })
                    .catch((error) => {
                        console.log('error', error);
                    });

            },
            countTeamWithAllTasks()
            {
                var vm = this,
                    teamArray = [];

                vm.collect(vm.teams)
                .each(team => {
                    
                    var tasksArr = [],
                        allTasks = [];

                    if( !vm.lodash.isEmpty(team.tasks) )
                    {
                        vm.collect(vm.pageStep.tasks)
                        .each(task => {

                            if( !vm.lodash.isEmpty(task.tracks) )
                            {
                                if( vm.collect(task.tracks).whereIn('id', vm.collect(team.tracks).pluck('id')).count() )
                                {
                                    if( !vm.lodash.isEmpty(task.options) && !vm.lodash.isEmpty(task.options.mandatory.toString()) && task.options.mandatory )
                                    {
                                        allTasks.push(task.id);

                                        if( vm.collect(team.tasks).contains('id', task.id) )
                                        {
                                            if( !vm.collect(tasksArr).contains(task.id) )
                                            {
                                                tasksArr.push(task.id);
                                            }
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if( !vm.lodash.isEmpty(task.options) && !vm.lodash.isEmpty(task.options.mandatory.toString()) && task.options.mandatory )
                                {
                                    allTasks.push(task.id);
                                    
                                    if( vm.collect(team.tasks).contains('id', task.id) )
                                    {
                                        if( !vm.collect(tasksArr).contains(task.id) )
                                        {
                                            tasksArr.push(task.id);
                                        }
                                    }
                                }
                            }

                        });
                    }

                    if( vm.collect(tasksArr).count() == vm.collect(allTasks).count() )
                    {
                        teamArray.push(team);
                    }

                });

                vm.countTeamsAllTasksArray = teamArray;
            },
            getSteps()
            {
                var vm = this;

                axios
                    .get(route('api.step.index'))
                    .then((response) => {
                        vm.stepsLists = response.data.data;
                        vm.$nextTick(() => {

                            vm.currentStep = collect(vm.stepsLists).firstWhere('is_current', true);

                            switch (vm.step.id)
                            {
                                case 2:

                                    vm.getUsers(1, 'quiz');

                                    vm.getTeams(1, 'tracksIdea');
                                    vm.getTeams(1, 'teamtracktakesurvey');
                                    vm.getTeams(1, 'teamtrackselectioncasepart2');
                                    vm.getTeams(1, 'teamtakequest');

                                break;
                                default:
                                break;
                            }

                        });
                    })
                    .catch((error) => {
                        console.log('error', error);
                    });
            },
            async getPageStep(step)
            {
                var vm = this;

                await axios
                    .get(route('api.step.index'), {
                        params:{
                            filter:{
                                id: step
                            },
                            with:[
                                'tasks',
                                'teamsreals',
                                'teamsreals.members',
                                // 'teams',
                                // 'teams.leads',
                                // 'teams.organization',
                                // 'teams.tasks',
                                // 'teams.idea',
                                // 'teams.passport',
                                // 'teams.members',
                            ],
                            withCount: [
                                'teams',
                                'teams_members',
                                'teamsreals',
                                // 'teamsreals_members'
                            ]
                        }
                    })
                    .then((response) => {

                        vm.pageStep = collect(response.data.data).first();

                        if( !vm.lodash.isEmpty(vm.pageStep.teamsreals) )
                        {
                            vm.pageStep.teamsreals_members_count = collect(vm.pageStep.teamsreals).sum((team) => collect(team.members).count());
                        }

                        vm.$nextTick(() => {
                            vm.getAllTeams();
                            vm.getAllUsers();
                            vm.getAllOrganizations();
                            vm.getSteps();
                        });

                    })
                    .catch((error) => {
                        console.log('error', error);
                    });
            },
            async getNextStep(step)
            {
                var vm = this;
                
                await axios
                    .get(route('api.step.index'), {
                        params:{
                            filter:{
                                id: step
                            },
                            with:[
                                // 'teams',
                                // 'teams.members',
                                'teamsreals',
                                'teamsreals.members',
                            ],
                            withCount: [
                                'teams',
                                'teamsreals',
                                'teams_members',
                                // 'teamsreals_members'
                            ]
                        }
                    })
                    .then((response) => {

                        vm.nextStep = collect(response.data.data).first();

                        if( !vm.lodash.isEmpty(vm.nextStep.teamsreals) )
                        {
                            vm.nextStep.teamsreals_members_count = collect(vm.nextStep.teamsreals).sum((team) => collect(team.members).count());
                        }

                    })
                    .catch((error) => {
                        console.log('error', error);
                    });
            },
            HelpersTableElementsEvent(event)
            {
                var vm = this;
                
                if( event.currentTarget.scrollLeft !== Number(event.target.getAttribute('data-scroll')) )
                {
                    event.target.querySelector('.table-notice-helper').remove();
                    event.target.removeEventListener('scroll', vm.HelpersTableElementsEvent);
                }
            }
        }
    }
</script>