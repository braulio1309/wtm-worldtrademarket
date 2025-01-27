<template>
    <div v-if="componentLoader" class="position-relative h-100">
        <app-overlay-loader />
    </div>
    <div v-else>
        <app-overlay-loader v-if="preloader" />
        <form class="mb-0" v-else ref="form" data-url="admin/auth/users/change-settings"
            :class="{ 'loading-opacity': preloader }">
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_first_name" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('first_name') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_first_name" type="text" :placeholder="$t('type_first_name')"
                            v-model="user.loggedInUser.first_name" :required="true" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_last_name" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('last_name') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_last_name" type="text" :placeholder="$t('type_last_name')"
                            v-model="user.loggedInUser.last_name" :required="true" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_email" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('email') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_email" type="email" :placeholder="$t('type_your_email')"
                            v-model="user.loggedInUser.email" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label class="text-left d-block mb-lg-2 mb-xl-0">{{ $t('gender') }}</label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input type="radio" :list="genderList" v-model="user.loggedInUser.gender" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_contact" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('contact_number') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_contact" type="tel-input" v-model="user.loggedInUser.contact"
                            :placeholder="$t('type_contact_number')" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_address" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('address') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_address" type="textarea" v-model="user.loggedInUser.address"
                            :placeholder="$t('type_address')" />
                    </div>
                </div>
            </div>
            <div class="form-group" v-if="user.loggedInUser.document">
                <div class="card-header p-primary bg-transparent">
                    <h5 class="card-title m-0">Cargue su documento de identidad</h5>
                </div>
                <app-overlay-loader v-if="preloader" />
                <div class="form-group mb-primary row align-items-center">
                    <div class="col-sm-12 default-file">
                        <input type="file" @change="handleFileChange" :label="'Cargue su archivo'" />
                        <small class="text-muted font-italic">
                            {{ $t('recommended_default_file_validation') }}
                        </small>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="alert alert-success" role="alert">
                    Su documento ha sido cargado con éxito.
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-lg-3 col-xl-3 d-flex align-items-center">
                        <label for="user_date_of_birth" class="text-left d-block mb-lg-2 mb-xl-0">
                            {{ $t('date_of_birth') }}
                        </label>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        <app-input id="user_date_of_birth" type="date" v-model="user.loggedInUser.date_of_birth" />
                    </div>
                </div>
            </div>
            <div class="form-group mb-0 mt-5">
                <div class="row">
                    <div class="col-12 action-buttons">
                        <button type="button" class="btn btn-primary mr-2" @click.prevent="submit">
                            {{ $t('save') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import GlobalHelperMixin from "../../../Mixins/global/GlobalHelperMixin";
import { FormMixin } from "../../../../core/mixins/form/FormMixin";
import { UserMixin } from "./Mixins/UserMixin";
import * as actions from "../../../Config/ApiUrl";
import AppLibrary from "../../../Helpers/AppLibrary";
import { urlGenerator } from "../../../Helpers/AxiosHelper";

export default {
    name: "PersonalInformation",
    extends: AppLibrary,
    mixins: [FormMixin, UserMixin, GlobalHelperMixin],
    data() {
        return {
            preloader: false,
            genderList: [
                {
                    id: 'male',
                    value: 'Male'
                },
                {
                    id: 'female',
                    value: 'Female'
                }
            ],
        }
    },
    methods: {
        beforeSubmit() {
            this.scrollToTop(true);
            this.preloader = true;
        },
        handleFileChange(event) {
            this.user.document = event.target.files[0]; // Obtener el archivo seleccionado
        },
        submit() {
            const formData = new FormData();
            // Iterar sobre las propiedades del objeto user
            Object.keys(this.user.loggedInUser).forEach((key) => {
                if (key === "document") {
                    formData.append(key, this.user.document); // Adjuntar el archivo
                } else {
                    formData.append(key, this.user[key]); // Adjuntar otras propiedades
                }
            });

            if (formData.date_of_birth) formData.date_of_birth = this.getDateFormatForBackend(formData.date_of_birth);
            else formData.date_of_birth = null;
            this.save(formData);
        },
        afterFinalResponse() {
            this.preloader = false;
        },
        afterSuccess(res) {
            this.$toastr.s(res.data.message);
            window.location = urlGenerator(actions.MY_PROFILE);
        },
        afterError(res) {
            this.$toastr.e(res.data.message);
        },
    }
}
</script>
