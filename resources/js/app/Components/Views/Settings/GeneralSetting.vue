<template>
    <div class="general-setting">
        <app-overlay-loader v-if="preloader" />
        <form v-else ref="form" data-url="/admin/app/settings" enctype="multipart/form-data" class="mb-0"
            :class="{ 'loading-opacity': preloader }">
            <!-- Company Info -->


            <!-- Date & Time Setting -->


            <!-- Currency Settings -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <div class="col-md-12">


                        <div class="form-group row">
                            <label for="appSettingsCurrencySymbol" class="col-sm-2 mb-sm-0">
                                Comisiones generales %
                            </label>
                            <div class="col-lg-8 col-xl-8 col-form-label">
                                <app-input id="appSettingsCurrencySymbol" type="number" v-model="appSettings.comisiones"
                                    :required="true" />
                            </div>
                        </div>
                        <div class="form-group row" v-for="(item, index) in commissions" :key="index">

                            <label class="col-sm-2 mb-sm-0">
                                Comisiones usuarios %
                            </label>
                            <app-input type="number" class="col-lg-3 col-xl-3 col-form-label" v-model="item.commission"
                                :required="true" />
                            <app-input type="multi-select" class="col-lg-3 col-xl-3 col-form-label" :list="list"
                                :isAnimatedDropdown="true" v-model="item.user" list-value-field="value" />

                            <div class="col-auto">
                                <button class="btn btn-danger" @click="removeCommission(index)">
                                    Eliminar
                                </button>
                            </div>
                        </div>


                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button class="btn btn-success" @click="addCommission">Añadir</button>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Currency Settings -->


            <div class="mt-5 action-buttons">
                <button class="btn btn-primary mr-2" @click.prevent="submit">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { FormMixin } from "../../../../core/mixins/form/FormMixin";
import { SettingsStoreMixin } from './Mixins/SettingsStoreMixin';
import * as actions from "../../../Config/ApiUrl";
export default {
    name: "GeneralSetting",
    mixins: [FormMixin, SettingsStoreMixin],
    data() {
        return {
            preloader: false,
            dateFormats: [],
            timeFormats: [],
            timeZones: [],
            decimalSeparators: [],
            numberOfDecimals: [],
            thousandSeparators: [],
            currencyPositions: [],
            layouts: [],
            registration: [],
            list: [],
            selected: [],
            list: [

            ],

            commissions: [
                {
                    commission: null,
                    user: null,
                },
            ],
        }
    },
    created() {
        this.data();
        this.$store.dispatch('getSettings');
    },
    computed: {
        languageList() {
            return this.$store.state.settings.languages
        },
    },
    methods: {
        removeCommission(index) {
            this.commissions.splice(index, 1);
        },
        addCommission() {
            this.commissions.push({ commission: null, user: null });
        },
        changeValue(type) {
            if (type === 'thousand_separator') {
                if (this.appSettings.thousand_separator === ',') {
                    this.appSettings.decimal_separator = '.'
                }
                else if (this.appSettings.thousand_separator === '.') {
                    this.appSettings.decimal_separator = ','
                }
            }
            else {
                this.appSettings.thousand_separator = this.appSettings.decimal_separator === ',' ? '.' : ','
            }
        },
        data() {
            this.preloader = true;
            let url = actions.GENERAL_SETTINGS;
            this.axiosGet(url).then(response => {
                console.log(response.data)
                this.currencyPositions = response.data.currency_position;
                this.dateFormats = response.data.date_format;
                this.decimalSeparators = response.data.decimal_separator;
                this.numberOfDecimals = response.data.number_of_decimal;
                this.thousandSeparators = response.data.thousand_separator;
                this.timeFormats = response.data.time_format;
                this.timeZones = response.data.time_zones;
                this.layouts = response.data.layouts;
                this.registration = response.data.registration;

                this.list = response.data.users;
                this.selected = response.data.selected,
                    this.commissions = (response.data.userComision.length == 0) ? [] : response.data.userComision
                console.log(this.commissions)

            }).catch(({ response }) => {
            }).finally(() => {
                this.preloader = false;
            });
        },
        beforeSubmit() {
            this.preloader = true;
        },
        submit() {
            let formData = new FormData;

            for (const [key, value] of Object.entries(this.appSettings)) {
                formData.append(key, value);
            }

            const imageProperties = ['company_logo', 'company_banner', 'company_icon'];
            for (const prop of imageProperties) {
                if (!(this.appSettings[prop] instanceof File)) {
                    formData.delete(prop);
                }
            }

            formData.append('comisiones', JSON.stringify(this.commissions))
            this.save(formData);
        },
        afterSuccess(res) {
            this.$toastr.s(res.data.message);
            window.location.reload();
        },
        afterFinalResponse() {
            this.preloader = false;
        },
        afterError(res) {
            this.$toastr.e(res.data.message);
        },
    }
}
</script>
