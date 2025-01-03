<template>
    <div class="position-relative">
        <div v-if="countCreatedResponse < 3" class="root-preloader position-absolute overlay-loader-wrapper">
            <div class="spinner-bounce d-flex align-items-center dashboard-preloader justify-content-center">
                <span class="bounce1 mr-1" />
                <span class="bounce2 mr-1" />
                <span class="bounce3 mr-1" />
                <span class="bounce4" />
            </div>
        </div>
        <div class="content-wrapper">
            <app-breadcrumb :page-title="'Principal'" :directory="'Menu principal'" :icon="'pie-chart'" />
            Bienvenido {{ user }} miembro desde {{ formatTimeAgo(this.userDate) }}
            <app-overlay-loader v-if="!defaultData && !mainPreloader" />
            <div class="row" v-if="defaultData">
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3"
                    v-for="(item, index) in info.getDefaultDashboardInfo.topSectionData" :key="index">
                    <app-widget class="mb-primary" :type="'app-widget-with-icon'" :label="item.label"
                        :number="numberFormat(item.number)" :icon="item.icon" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-9 mb-primary">
                    <app-overlay-loader v-if="!audienceChart && !mainPreloader" />
                    <div class="card card-with-shadow border-0 h-100" v-if="audienceChart">
                        <div
                            class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Historico de depositos y retiros</h5>
                            <ul class="nav tab-filter-menu justify-content-flex-end">
                                <li class="nav-item"
                                    v-for="(item, index) in info.audienceOverviewChart.chartFilterOptions" :key="index">
                                    <a href="#" class="nav-link py-0"
                                        :class="[audienceFilter == item.id ? 'active' : index === 0 && audienceFilter === '' ? 'active' : '']"
                                        @click.prevent="audienceOverviewCartFilterValue(item.id)">
                                        {{ item.value }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <app-chart class="mb-primary" type="custom-line-chart" :height="170"
                                :labels="info.audienceOverviewChart.labels"
                                :data-sets="info.audienceOverviewChart.chartData" />
                            <div class="chart-data-list d-flex flex-wrap justify-content-center">
                                <div class="data-group-item" style="color: #368cd5;">
                                    <span class="square" style="background-color: #368cd5;" />
                                    Retiros

                                    <span class="value">{{ info.audienceOverviewChart.user }}</span>
                                </div>
                                <div class="data-group-item" style="color: #f7531e;">
                                    <span class="square" style="background-color: #f7531e;" />
                                    Depositos

                                    <span class="value">{{ info.audienceOverviewChart.visitor }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <app-overlay-loader v-if="!defaultData && !mainPreloader" />
                <div class="col-12 col-md-3 mb-4 mb-md-0" v-if="defaultData">
                    <div :class="{ 'mb-primary': index !== info.getDefaultDashboardInfo.circleTopSection.length - 1 }"
                        v-for="(item, index) in info.getDefaultDashboardInfo.circleTopSection" :key="index">
                        <app-widget :type="'app-widget-with-circle'" :label="item.label"
                            :number="numberFormat(item.number)" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12">
                    <app-overlay-loader v-if="!defaultData && !mainPreloader" />
                    <div class="row" v-if="defaultData">
                        <div class="col-12 col-sm-6 col-md-6"
                            v-for="(item, index) in info.getDefaultDashboardInfo.bottomSectionData" :key="index">
                            <app-widget class="mb-primary" :type="'app-widget-with-icon'" :label="item.label"
                                :number="numberFormat(item.number)" :icon="item.icon" />
                        </div>
                    </div>

                    <div class="row" v-if="defaultData">
                        <div class="col-12 col-md-6 mb-4 mb-md-0"
                            v-for="(item, index) in info.getDefaultDashboardInfo.circleBottomSection" :key="index">
                            <app-widget :type="'app-widget-with-circle'" :label="item.label"
                                :number="numberFormat(item.number)" />
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</template>

<script>
import { FormMixin } from '../../../../../core/mixins/form/FormMixin';
import { DashboardPreloader } from "./Mixins/DashboardPreloader";
import * as actions from '../../../../Config/ApiUrl';
import { numberFormatter } from "../../../../Helpers/Helpers";

export default {

    mixins: [FormMixin, DashboardPreloader],
    name: "Dashboard",
    data() {
        return {
            /* Loader section and active/inactive section */
            mainPreloader: true,

            audienceChart: false,
            browserChart: false,
            defaultData: false,
            /* end */

            audienceFilter: 'this_week',
            browserFilter: 'this_week',
            info: {},
            user: window.Laravel?.name || null,
            userDate: window.Laravel?.date || null,
        }
    },
    created() {
        this.mainPreloader = true;
        this.getDefaultDashboardInfo();
        this.audienceOverviewCart();
        this.browserOverviewCart();
    },
    mounted() {
        console.log(this.userDate)
    },
    methods: {
        formatTimeAgo(date) {
            console.log(date)
            const now = new Date();
            const diffInSeconds = Math.floor((now - new Date(date)) / 1000); // Diferencia en segundos
            const diffInMinutes = Math.floor(diffInSeconds / 60); // Diferencia en minutos
            const diffInHours = Math.floor(diffInMinutes / 60); // Diferencia en horas
            const diffInDays = Math.floor(diffInHours / 24); // Diferencia en días
            const diffInMonths = Math.floor(diffInDays / 30); // Aproximación a meses
            const diffInYears = Math.floor(diffInMonths / 12); // Aproximación a años

            if (diffInYears > 0) {
                return `hace ${diffInYears} ${diffInYears === 1 ? 'año' : 'años'}`;
            } else if (diffInMonths > 0) {
                return `hace ${diffInMonths} ${diffInMonths === 1 ? 'mes' : 'meses'}`;
            } else if (diffInDays > 0) {
                return `hace ${diffInDays} ${diffInDays === 1 ? 'día' : 'días'}`;
            } else if (diffInHours > 0) {
                return `hace ${diffInHours} ${diffInHours === 1 ? 'hora' : 'horas'}`;
            } else if (diffInMinutes > 0) {
                return `hace ${diffInMinutes} ${diffInMinutes === 1 ? 'minuto' : 'minutos'}`;
            } else {
                return `hace ${diffInSeconds} ${diffInSeconds === 1 ? 'segundo' : 'segundos'}`;
            }
        },
        audienceOverviewCartFilterValue(item) {
            this.audienceFilter = item;
            this.audienceChart = false;
            this.audienceOverviewCart();

        },
        browserOverviewChartFilterValue(item) {

            this.browserFilter = item;
            this.browserChart = false;
            this.browserOverviewCart();
        },

        getDefaultDashboardInfo() {

            let url = actions.DEFAULT_DASHBOARD_INFO;

            this.axiosGet(url).then(response => {

                this.info.getDefaultDashboardInfo = response.data;
                this.defaultData = true;

            }).catch(({ response }) => {

            }).finally(() => {
                this.mainPreloader = false;
                this.countCreatedResponse++;
            });
        },

        audienceOverviewCart() {

            let url = actions.AUDIENCE_OVERVIEW_CHART, reqData = {};

            reqData.params = {
                key: this.audienceFilter
            };

            this.axiosGet(
                url,
                reqData
            ).then(response => {

                this.info.audienceOverviewChart = response.data;
                this.audienceChart = true;

            }).catch(({ response }) => {

            }).finally(() => {
                this.mainPreloader = false;
                this.countCreatedResponse++;
            });
        },

        browserOverviewCart() {
            let url = actions.BROWSER_OVERVIEW_CHART, reqData = {};

            reqData.params = {
                key: this.browserFilter
            };

            this.axiosGet(
                url,
                reqData
            ).then(response => {

                this.info.browserOverviewChart = response.data;
                this.browserChart = true;

            }).catch(({ response }) => {

            }).finally(() => {
                this.mainPreloader = false;
                this.countCreatedResponse++;
            });
        },

        numberFormat(value) {
            return numberFormatter(value);
        }

    }
}
</script>
